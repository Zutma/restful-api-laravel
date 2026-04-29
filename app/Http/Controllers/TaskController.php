<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskAttachTagRequest;
use App\Http\Requests\TaskAssignRequest;
use App\Http\Requests\TaskCreateRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Http\Resources\TaskResource;
use App\Models\Tag;
use App\Models\Task;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskController extends Controller
{
    public function list(Request $request): JsonResponse
    {
        $user = Auth::user();
        $tasks = Task::with(['tags', 'assignees', 'user']);

        if (!$user->can('task-list')) {
            if ($user->hasRole('supervisor')) {
                $tasks->where('user_id', $user->id);
            } elseif ($user->hasRole('user')) {
                $tasks->whereRelation('assignees', 'user_id', $user->id);
            }
        }
        $tasks = $tasks->get();

        return (TaskResource::collection($tasks))->response();
    }

    public function create(TaskCreateRequest $request): JsonResponse
    {
        $this->authorize('create', Task::class);
        $user = Auth::user();
        $data = $request->validated();

        $task = new Task($data);
        $task->user_id = $user->id;
        $task->save();

        return (new TaskResource($task))->response()->setStatusCode(201);
    }

    public function get(int $idTask): JsonResponse
    {
        $user = Auth::user();

        $query = Task::with(['tags', 'assignees', 'user'])->where('id', $idTask);

        if (!$user->can('task-list')) {
            if ($user->hasRole('supervisor')) {
                $query->where('user_id', $user->id);
            } elseif ($user->hasRole('user')) {
                $query->whereRelation('assignees', 'user_id', $user->id);
            }
        }

        $task = $query->first();

        if (!$task) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => ['not found']
                ]
            ], 404));
        }

        return (new TaskResource($task))->response();
    }

    public function update(int $idTask, TaskUpdateRequest $request): JsonResponse
    {
        $task = Task::where('id', $idTask)->first();
        if (!$task) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => ['not found']
                ]
            ], 404));
        }

        $this->authorize('update', $task);

        $data = $request->validated();
        $task->fill($data);
        $task->save();

        return (new TaskResource($task))->response();
    }

    public function delete(int $idTask): JsonResponse
    {
        $task = Task::where('id', $idTask)->first();
        if (!$task) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => ['not found']
                ]
            ], 404));
        }

        $this->authorize('delete', $task);

        $task->delete();

        return response()->json([
            'data' => true,
        ], 200);
    }

    public function attachTag(int $idTask, TaskAttachTagRequest $request): JsonResponse
    {
        $user = Auth::user();
        $task = Task::where('id', $idTask)->first();
        if (!$task) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => ['not found']
                ]
            ], 404));
        }

        $this->authorize('update', $task);

        $data = $request->validated();
        $idTag = $data['tag_id'];
        $tagQuery = Tag::where('id', $idTag);
        if (!$user->hasRole('admin')) {
            $tagQuery->where('user_id', $user->id);
        }
        $tag = $tagQuery->first();
        if (!$tag) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => ['not found']
                ]
            ], 404));
        }

        $task->tags()->syncWithoutDetaching([$idTag]);

        activity()
            ->performedOn($task)
            ->withProperties(['tag' => $tag->name])
            ->log("Attached tag '{$tag->name}' to task");

        return response()->json([
            'data' => true,
        ], 200);
    }

    public function detachTag(int $idTask, int $idTag): JsonResponse
    {
        $user = Auth::user();
        $task = Task::where('id', $idTask)->first();
        if (!$task) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => ['not found']
                ]
            ], 404));
        }

        $this->authorize('update', $task);

        $tagQuery = Tag::where('id', $idTag);
        if (!$user->hasRole('admin')) {
            $tagQuery->where('user_id', $user->id);
        }
        $tag = $tagQuery->first();
        if (!$tag) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => ['not found']
                ]
            ], 404));
        }

        if (!$task->tags()->where('tag_id', $idTag)->exists()) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => ['not found']
                ]
            ], 404));
        }

        $task->tags()->detach($idTag);

        activity()
            ->performedOn($task)
            ->withProperties(['tag' => $tag->name])
            ->log("Detached tag '{$tag->name}' from task");

        return response()->json([
            'data' => true,
        ], 200);
    }

    public function attachAssignee(int $idTask, TaskAssignRequest $request): JsonResponse
    {
        $task = Task::where('id', $idTask)->first();
        if (!$task) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => ['not found']
                ]
            ], 404));
        }

        $this->authorize('update', $task);

        $data = $request->validated();
        $assignId = $data['user_id'];
        $task->assignees()->syncWithoutDetaching([$assignId]);

        $assignee = User::find($assignId);
        activity()
            ->performedOn($task)
            ->withProperties(['assignee' => $assignee->name])
            ->log("Assigned user '{$assignee->name}' to task");

        return response()->json([
            'data' => true,
        ], 200);
    }

    public function detachAssignee(int $idTask, int $idUser): JsonResponse
    {
        $task = Task::where('id', $idTask)->first();
        if (!$task) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => ['not found']
                ]
            ], 404));
        }

        $this->authorize('update', $task);

        if (!$task->assignees()->where('user_id', $idUser)->exists()) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => ['not found']
                ]
            ], 404));
        }

        $task->assignees()->detach($idUser);

        $assignee = User::find($idUser);
        activity()
            ->performedOn($task)
            ->withProperties(['assignee' => $assignee->name])
            ->log("Unassigned user '{$assignee->name}' from task");

        return response()->json([
            'data' => true,
        ], 200);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\TaskAttachTagRequest;
use App\Http\Requests\TaskAssignRequest;
use App\Http\Requests\TaskCreateRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Http\Resources\TaskResource;
use App\Models\Tag;
use App\Models\Task;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\JsonResponse;

class TaskController extends Controller
{

    public function create(TaskCreateRequest $request): JsonResponse {
        $data = $request->validated();
        $user = Auth::user();

        $task = new Task($data);
        $task->user_id = $user->id;
        $task->save();

        return (new TaskResource($task))/*->additional(['errors' => null])*/->response()->setStatusCode(201);
    }

    public function list(Request $request): JsonResponse {
        $user = Auth::user();

        $tasks = Task::with(['tags', 'assignees']);

        if($user->role !== 'admin'){
            $tasks->where('user_id', $user->id);
        }

        $tasks = $tasks->get();

        return (TaskResource::collection($tasks))/*->additional(['errors' => null])*/->response();
    }

    public function get(int $idTask): JsonResponse {
        $user = Auth::user();

        $task = Task::with(['tags', 'assignees'])->where('id', $idTask)->first();

        if (!$task) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => ['not found']
                ]
            ], 404));
        }

        $this->authorize('view', $task);

        return (new TaskResource($task))/*->additional(['errors' => null])*/->response();
    }

    public function update(int $idTask, TaskUpdateRequest $request): JsonResponse {
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
        $task->fill($data);
        $task->save();

        return (new TaskResource($task))/*->additional(['errors' => null])*/->response();
    }

    public function delete(int $idTask): JsonResponse {
        $user = Auth::user();

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
            // 'errors' => null
        ], 200);
    }

    public function attachTag(int $idTask, TaskAttachTagRequest $request): JsonResponse {
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

        $tag = Tag::where('id', $idTag)->where('user_id', $user->id)->first();

        if (!$tag) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => ['not found']
                ]
            ], 404));
        }

        $task->tags()->syncWithoutDetaching([$idTag]);

        return response()->json([
            'data' => true,
            // 'errors' => null
        ], 200);
    }

    public function detachTag(int $idTask, int $idTag): JsonResponse {
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

        $tag = Tag::where('id', $idTag)->where('user_id', $user->id)->first();

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

        return response()->json([
            'data' => true,
            // 'errors' => null
        ], 200);
    }

    public function attachAssignee(int $idTask, TaskAssignRequest $request): JsonResponse {
        $user = Auth::user();

        $task = Task::where('id', $idTask)->first();

        if (!$task) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => ['not found']
                ]
            ], 404));
        }
        $this->authorize('update',$task);

        $data = $request->validated();
        $assignId = $data['user_id'];

        $task->assignees()->syncWithoutDetaching([$assignId]);

        return response()->json([
            'data' => true,
            // 'errors' => null
        ], 200);
    }

    public function detachAssignee(int $idTask, int $idUser): JsonResponse {
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

        if (!$task->assignees()->where('user_id', $idUser)->exists()) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => ['not found']
                ]
            ], 404));
        }

        $task->assignees()->detach($idUser);

        return response()->json([
            'data' => true,
            // 'errors' => null
        ], 200);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentCreateRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Task;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function create(int $idTask, CommentCreateRequest $request): JsonResponse{
        $user = Auth::user();

        $task = Task::where('id', $idTask)->first();

        if(!$task){
            throw new HttpResponseException(response()->json([
                'errors'=>[
                    'message'=>['not found']
                ]
            ],404));
        }

        $this->authorize('view', $task);

        $data = $request->validated();

        $comment = $task->comments()->create([
            'content'=>$data['content'],
            'user_id'=>$user->id
        ]);

        return (new CommentResource($comment))/*->additional(['errors'=> null])*/->response()->setStatusCode(201);
    }

    public function list(int $idTask):JsonResponse{
        $user = Auth::user();

        $task = Task::where('id',$idTask)->first();

        if(!$task){
            throw new HttpResponseException(response()->json([
                'errors'=>[
                    'message'=>['not found']
                ]
            ],404));
        }
        
        $this->authorize('view', $task);

        $comments = $task->comments()->with('user')->get();

        return (CommentResource::collection($comments))/*->additional(['errors'=>null])*/->response();
    }

    public function delete(int $idTask, int $idComment): JsonResponse{
        $user = Auth::user();

        $task = Task::where('id', $idTask)->first();

        if(!$task){
            throw new HttpResponseException(response()->json([
                'errors'=>[
                    'message'=>['not found']
                ]
            ],404));
        }

        $comment = Comment::where('id', $idComment)->first();

        if(!$comment){
            throw new HttpResponseException(response()->json([
                'errors'=>[
                    'message'=>['not found']
                ]
            ],404));
        }

        $this->authorize('delete', $comment);

        $comment->delete();
        return response()->json([
            'data' => true,
            // 'errors' => null
        ], 200);
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\TagCreateRequest;
use App\Http\Requests\TagUpdateRequest;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\JsonResponse;

class TagController extends Controller
{
    public function create(TagCreateRequest $request): JsonResponse{
        $data = $request->validated();
        $user = Auth::user();

        $tag = new Tag($data);
        $tag->user_id=$user->id;
        $tag->save();

        return (new TagResource($tag))/*->additional([
            'errors'=>null
        ])*/->response()->setStatusCode(201);
    }

    public function list(): JsonResponse{
        $user = Auth::user();

        $tags = Tag::where('user_id', $user->id)->get();

        return (TagResource::collection($tags))/*->additional(['errors'=>null])*/->response();
    }

    public function get(int $idTag): JsonResponse {
        $user = Auth::user();

        $tag = Tag::where('id', $idTag)->where('user_id', $user->id)->first();

        if (!$tag) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => ['not found']
                ]
            ], 404));
        }

        return (new TagResource($tag))/*->additional(['errors' => null])*/->response();
    }

    public function update(int $idTag, TagUpdateRequest $request): JsonResponse {
        $user = Auth::user();

        $tag = Tag::where('id', $idTag)->where('user_id', $user->id)->first();

        if (!$tag) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => ['not found']
                ]
            ], 404));
        }

        $data = $request->validated();
        $tag->fill($data);
        $tag->save();

        return (new TagResource($tag))/*->additional(['errors' => null])*/->response();
    }

    public function delete(int $idTag): JsonResponse {
        $user = Auth::user();

        $tag = Tag::where('id', $idTag)->where('user_id', $user->id)->first();

        if (!$tag) {
            throw new HttpResponseException(response()->json([
                'errors' => [
                    'message' => ['not found']
                ]
            ], 404));
        }

        $tag->delete();
        return response()->json([
            'data' => true,
            // 'errors' => null
        ], 200);
    }
}   

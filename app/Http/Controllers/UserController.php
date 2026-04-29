<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserUpdateRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function register(UserRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = new User($data);
        $user->password = Hash::make($data['password']);
        $user->save();

        if (isset($data['role'])){
            $user->assignRole($data['role']);
        }else{
            $user->assignRole('user');
        }
        return (new UserResource($user))->additional(['errors' => null])->response()->setStatusCode(201);
    }

    public function login(UserLoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::with('roles')->where('username', $data['username'])->first();
        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new HttpResponseException(response()->json([
                "errors" => [
                    "message" => ["username or password wrong"]
                ]
            ], 401));
        }

        $user->token = Str::uuid()->toString();
        $user->save();

        return (new UserResource($user))->additional([
            'data' => ['token' => $user->token],
            // 'errors' => null
        ])->response();
    }

    public function get(Request $request): JsonResponse
    {
        $user = Auth::user();
        $user->load('roles');
        return (new UserResource($user))->additional([
            'data' => ['token' => $user->token],
            // 'errors' => null
        ])->response();
    }

    public function update(UserUpdateRequest $request): JsonResponse
    {
        $data = $request->validated();
        $user = Auth::user();

        if (isset($data['name'])) {
            $user->name = $data['name'];
        }

        if (isset($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();
        return (new UserResource($user))/*->additional(['errors' => null])*/->response();
    }

    public function logout(Request $request): JsonResponse
    {
        $user = Auth::user();
        $user->token = null;
        $user->save();

        return response()->json([
            "data" => true,
            // "errors" => null
        ], 200);
    }
}

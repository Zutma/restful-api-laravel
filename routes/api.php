<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\ApiAuthMiddleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/users',[UserController::class,'register']);
Route::post('/users/login',[UserController::class,'login']);

Route::middleware(ApiAuthMiddleware::class)->group(function(){
    Route::get('/users/current',[UserController::class,'get']);
    Route::patch('users/current', [UserController::class,'update']);
    Route::delete('users/logout',[UserController::class,'logout']);

    Route::post('/contacts', [ContactController::class,'create']);
    Route::get('/contacts', [ContactController::class,'search']);
    Route::get('/contacts/{idContact}', [ContactController::class,'get'])->where('idContact', '[0-9]+');
    Route::put('/contacts/{idContact}', [ContactController::class,'update'])->where('idContact', '[0-9]+');
    Route::delete('/contacts/{idContact}', [ContactController::class,'delete'])->where('idContact', '[0-9]+');

    Route::post('/contacts/{idContact}/addresses', [AddressController::class,'create'])->where('idContact', '[0-9]+');
    Route::get('/contacts/{idContact}/addresses/{idAddress}', [AddressController::class,'get'])->where('idContact', '[0-9]+')->where('idAddress', '[0-9]+');
    Route::put('/contacts/{idContact}/addresses/{idAddress}', [AddressController::class,'update'])->where('idContact', '[0-9]+')->where('idAddress', '[0-9]+');
    Route::delete('/contacts/{idContact}/addresses/{idAddress}', [AddressController::class,'delete'])->where('idContact', '[0-9]+')->where('idAddress', '[0-9]+');
    Route::get('/contacts/{idContact}/addresses', [AddressController::class,'list'])->where('idContact', '[0-9]+');

    Route::post('/tasks',[TaskController::class,'create']);
    Route::get('/tasks',[TaskController::class,'list']);
    Route::get('/tasks/{idTask}',[TaskController::class,'get'])->where('idTask','[0-9]+');
    Route::put('/tasks/{idTask}',[TaskController::class,'update'])->where('idTask','[0-9]+');
    Route::delete('/tasks/{idTask}',[TaskController::class,'delete'])->where('idTask','[0-9]+');
    Route::post('/tasks/{idTask}/tags',[TaskController::class,'attachTag'])->where('idTask','[0-9]+');
    Route::delete('/tasks/{idTask}/tags/{idTag}',[TaskController::class,'detachTag'])->where('idTask','[0-9]+')->where('idTag','[0-9]+');
    Route::post('/tasks/{idTask}/assignees',[TaskController::class,'attachAssignee'])->where('idTask','[0-9]+');
    Route::delete('/tasks/{idTask}/assignees/{idUser}',[TaskController::class,'detachAssignee'])->where('idTask','[0-9]+')->where('idUser','[0-9]+');

    Route::post('/tags',[TagController::class,'create']);
    Route::get('/tags',[TagController::class,'list']);
    Route::get('/tags/{idTag}',[TagController::class,'get'])->where('idTag','[0-9]+');
    Route::put('/tags/{idTag}',[TagController::class,'update'])->where('idTag','[0-9]+');
    Route::delete('/tags/{idTag}',[TagController::class,'delete'])->where('idTag','[0-9]+');
    
    Route::post('/tasks/{idTask}/comments',[CommentController::class,'create'])->where('idTask','[0-9]+');
    Route::get('/tasks/{idTask}/comments',[CommentController::class,'list'])->where('idTask','[0-9]+');
    Route::delete('/tasks/{idTask}/comments/{idComment}',[CommentController::class,'delete'])->where('idTask','[0-9]+')->where('idComment','[0-9]+');
});

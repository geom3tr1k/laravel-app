<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/registration', [AuthController::class, 'reg']);
Route::post('/authorization', [AuthController::class, 'authorization']);
Route::get('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/tasks', [TaskController::class, 'add'])->middleware('auth:sanctum');
Route::get("/tasks/disk", [TaskController::class, "disk"])->middleware('auth:sanctum');
Route::get("/tasks/shared", [TaskController::class, "shared"])->middleware('auth:sanctum');
Route::post('/tasks/{id}/access', [TaskController::class, 'addUser'])->middleware('auth:sanctum');
Route::delete('/tasks/{id}/access', [TaskController::class, 'deleteUser'])->middleware('auth:sanctum');
Route::delete('/tasks/{id}', [TaskController::class, 'deleteTask'])->middleware('auth:sanctum');
Route::post('/tasks/{id}', [TaskController::class, 'updateTitle'])->middleware('auth:sanctum');

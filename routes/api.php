<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TaskController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


// auth
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class ,'register']);
    Route::post('/login', [AuthController::class ,'login']);
});

Route::prefix('tasks')->middleware('auth:sanctum')->group(function () {
    Route::post('', [TaskController::class, 'store']);
    Route::get('', [TaskController::class, 'index']);
    Route::get('/deleted', [TaskController::class, 'deletedTasks']);
    Route::get('/{id}', [TaskController::class, 'show']);
    Route::put('/{id}', [TaskController::class, 'update']);
    Route::delete('/{id}', [TaskController::class, 'destroy']);
});



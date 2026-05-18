<?php

use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);

Route::get('books', [BookController::class, 'index']);
Route::post('books/show', [BookController::class, 'show']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);

    Route::post('books', [BookController::class, 'store']);
    Route::put('books', [BookController::class, 'update']);
    Route::patch('books', [BookController::class, 'update']);
    Route::delete('books', [BookController::class, 'destroy']);
});

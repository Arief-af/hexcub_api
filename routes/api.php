<?php

use App\Http\Controllers\VideoReviewController;
use App\Http\Controllers\VideoDetailController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\MeetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Contact CRUD Routes
Route::get('/contacts', [ContactController::class, 'index']);
Route::post('/contacts', [ContactController::class, 'store']);
Route::get('/contacts/{id}', [ContactController::class, 'show']);
Route::put('/contacts/{id}', [ContactController::class, 'update']);
Route::delete('/contacts/{id}', [ContactController::class, 'destroy']);

// Meet CRUD Routes
Route::get('/meets', [MeetController::class, 'index']);
Route::post('/meets', [MeetController::class, 'store']);
Route::get('/meets/{id}', [MeetController::class, 'show']);
Route::put('/meets/{id}', [MeetController::class, 'update']);
Route::delete('/meets/{id}', [MeetController::class, 'destroy']);

// Video CRUD Routes
Route::get('videos', [VideoController::class, 'index']);
Route::post('videos', [VideoController::class, 'store']);
Route::get('videos/{id}', [VideoController::class, 'show']);
Route::put('videos/{id}', [VideoController::class, 'update']);
Route::delete('videos/{id}', [VideoController::class, 'destroy']);

// User CRUD Routes
Route::get('users', [UserController::class, 'index']);
Route::post('users', [UserController::class, 'store']);
Route::get('users/{id}', [UserController::class, 'show']);
Route::put('users/{id}', [UserController::class, 'update']);
Route::delete('users/{id}', [UserController::class, 'destroy']);

// VideoDetail Routes
Route::get('video-details', [VideoDetailController::class, 'index']);
Route::post('video-details', [VideoDetailController::class, 'store']);
Route::get('video-details/{id}', [VideoDetailController::class, 'show']);
Route::put('video-details/{id}', [VideoDetailController::class, 'update']);
Route::delete('video-details/{id}', [VideoDetailController::class, 'destroy']);

// VideoReview Routes
Route::get('video-reviews', [VideoReviewController::class, 'index']);
Route::post('video-reviews', [VideoReviewController::class, 'store']);
Route::get('video-reviews/{id}', [VideoReviewController::class, 'show']);
Route::put('video-reviews/{id}', [VideoReviewController::class, 'update']);
Route::delete('video-reviews/{id}', [VideoReviewController::class, 'destroy']);
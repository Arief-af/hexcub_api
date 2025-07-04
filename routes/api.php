<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\UserVideoController;
use App\Http\Controllers\VideoCommentController;
use App\Http\Controllers\VideoReviewController;
use App\Http\Controllers\VideoDetailController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\MeetController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Container\Attributes\Auth;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware(['auth:sanctum', 'verified']);

Route::get('/stream/{filename}', [VideoController::class, 'stream']);
// Contact CRUD Routes
Route::get('/contacts', [ContactController::class, 'index']);
Route::post('/contacts', [ContactController::class, 'store']);
Route::get('/contacts/{id}', [ContactController::class, 'show']);
Route::delete('/contacts/{id}', [ContactController::class, 'destroy'])->middleware(['auth:sanctum', 'verified', AdminMiddleware::class]);

// Meet CRUD Routes
Route::get('/meets', [MeetController::class, 'index']);
Route::get('/meets/{id}', [MeetController::class, 'show']);
Route::middleware([AdminMiddleware::class, 'auth:sanctum'])->group(function () {
    Route::post('/meets', [MeetController::class, 'store']);
    Route::put('/meets/{id}', [MeetController::class, 'update']);
    Route::delete('/meets/{id}', [MeetController::class, 'destroy']);
});

// Video CRUD Routes
Route::get('videos', [VideoController::class, 'index']);
Route::get('videos/{id}', [VideoController::class, 'show']);
Route::middleware([AdminMiddleware::class, 'auth:sanctum'])->group(function () {
    Route::post('videos', [VideoController::class, 'store']);
    Route::put('videos/{id}', [VideoController::class, 'update']);
    Route::delete('videos/{id}', [VideoController::class, 'destroy']);
});

// User CRUD Routes
Route::get('users', [UserController::class, 'index']);
Route::post('users', [UserController::class, 'store']);
Route::get('users/{id}', [UserController::class, 'show']);
Route::put('users/profile/update', [UserController::class, 'update'])->middleware('auth:sanctum');
Route::put('users/{id}', [UserController::class, 'update']);

// VideoDetail Routes
Route::get('video-details', [VideoDetailController::class, 'index'])->middleware('auth:sanctum');
Route::get('video-details/{id}', [VideoDetailController::class, 'show']);
Route::middleware([AdminMiddleware::class, 'auth:sanctum'])->group(function () {
    Route::post('video-details', [VideoDetailController::class, 'store']);
    Route::put('video-details/{id}', [VideoDetailController::class, 'update']);
    Route::delete('video-details/{id}', [VideoDetailController::class, 'destroy']);
});

// VideoReview Routes
Route::get('video-reviews', [VideoReviewController::class, 'index']);
Route::post('video-reviews', [VideoReviewController::class, 'store']);
Route::get('video-reviews/{id}', [VideoReviewController::class, 'show']);
Route::put('video-reviews/{id}', [VideoReviewController::class, 'update']);
Route::delete('video-reviews/{id}', [VideoReviewController::class, 'destroy']);

// VideoComment Routes
Route::get('video-comments', [VideoCommentController::class, 'index']);
Route::post('video-comments', [VideoCommentController::class, 'store']);
Route::get('video-comments/{id}', [VideoCommentController::class, 'show']);
Route::put('video-comments/{id}', [VideoCommentController::class, 'update']);
Route::delete('video-comments/{id}', [VideoCommentController::class, 'destroy']);

// UserVideo Routes
Route::get('user-videos', [UserVideoController::class, 'index'])->middleware('auth:sanctum');
Route::post('user-videos', [UserVideoController::class, 'store'])->middleware('auth:sanctum');
Route::get('user-videos/{id}', [UserVideoController::class, 'show']);
Route::put('user-videos/{id}', [UserVideoController::class, 'update']);
Route::delete('user-videos/{id}', [UserVideoController::class, 'destroy']);

// Authetication Routes
Route::controller(AuthenticationController::class)->group(function(){
    Route::post('register', 'register');
    Route::post('login', 'login');
    Route::post('verify-email', 'verifyEmail')->name('verification.verify');
    Route::post('forgot-password', 'forgotPassword');
    Route::post('reset-password', 'resetPassword');
});
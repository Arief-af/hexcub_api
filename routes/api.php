<?php

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
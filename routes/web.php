<?php

use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'Hi' => "Hayo mau ngapain kamu? HAHAHAHAHAH",
    ]);
});

Route::get('/stream/{filename}', [VideoController::class, 'stream']);

<?php

use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'Hi' => "Hayo mau ngapain kamu? HAHAHAHAHAH",
        'version' => "1.0.0",
        "website" => "https://hexcub.zqdevs.my.id"
    ]);
});

Route::get('/stream/{filename}', [VideoController::class, 'stream']);

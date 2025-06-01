<?php

use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $uploadMaxFilesize = ini_get('upload_max_filesize');
    $postMaxSize = ini_get('post_max_size');
    $phpVersion = phpversion();
    $location = ini_get('extension_dir');

    // Ambil konfigurasi mail dari .env menggunakan env()
    $mailConfig = [
        'MAIL_MAILER' => env('MAIL_MAILER'),
        'MAIL_HOST' => env('MAIL_HOST'),
        'MAIL_PORT' => env('MAIL_PORT'),
        'MAIL_USERNAME' => env('MAIL_USERNAME'),
        'MAIL_PASSWORD' => env('MAIL_PASSWORD'),
        'MAIL_ENCRYPTION' => env('MAIL_ENCRYPTION'),
        'MAIL_FROM_ADDRESS' => env('MAIL_FROM_ADDRESS'),
        'MAIL_FROM_NAME' => env('MAIL_FROM_NAME'),
    ];

    return response()->json([
        'php_version' => $phpVersion,
        'upload_max_filesize' => $uploadMaxFilesize,
        'post_max_size' => $postMaxSize,
        'location' => $location,
        'mail_config' => $mailConfig,  // tambahkan mail config di response
    ]);
});

Route::get('/stream/{filename}', [VideoController::class, 'stream']);

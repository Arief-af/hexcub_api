<?php

use App\Http\Controllers\VideoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    $uploadMaxFilesize = ini_get('upload_max_filesize');
    $postMaxSize = ini_get('post_max_size');
    $phpVersion = phpversion();
    $location = ini_get('extension_dir'); // fixed this line
    
    return response()->json([
        'php_version' => $phpVersion,
        'upload_max_filesize' => $uploadMaxFilesize,
        'post_max_size' => $postMaxSize,
        'location' => $location // added location value
    ]);
});

Route::get('/stream/{filename}', [VideoController::class, 'stream']);

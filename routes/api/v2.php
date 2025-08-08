<?php

use App\Http\Controllers\Api\v2\TracksApiController;
use Illuminate\Support\Facades\Route;

Route::get('/tracks', [TracksApiController::class, 'listTracks']);
Route::get('/tracks/all', [TracksApiController::class, 'listAllTracks']);

<?php

use App\Http\Controllers\Api\PluginDataController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/plugins', [PluginDataController::class, 'allRclMessages']);
Route::get('/plugins-rcl', [PluginDataController::class, 'allRclMessages']);

Route::get('/clx-messages', [PluginDataController::class, 'detailedClxMessages']);

Route::get('/tracks', [PluginDataController::class, 'getTracks']);

if (config('app.debug')) {
    Route::get('/ctp-bookings', function () {
       return \App\Models\CtpBooking::all();
    });
}

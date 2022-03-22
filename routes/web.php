<?php

use App\Http\Controllers\AdministrationController;
use App\Http\Controllers\RclMessagesController;
use App\Http\Controllers\VatsimAuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
})->name('welcome');

Route::prefix('auth')->name('auth')->group(function () {
    Route::get('/redirect', [VatsimAuthController::class, 'redirect'])->name('.redirect');
    Route::get('/authenticate', [VatsimAuthController::class, 'authenticate'])->name('.authenticate');
    Route::get('/deauthenticate', [VatsimAuthController::class, 'deauthenticate'])->name('.deauthenticate');
});;


Route::prefix('administration')->name('administration')->middleware('can:administrate')->group(function () {
    Route::get('/', [AdministrationController::class, 'index'])->name('index');
    Route::get('/accounts', [AdministrationController::class, 'accounts'])->name('.accounts');
    Route::post('/accounts/add-access', [AdministrationController::class, 'addAccess'])->name('.accounts.add-access');
    Route::post('/accounts/remove-access', [AdministrationController::class, 'removeAccess'])->name('.accounts.remove-access');
});

Route::prefix('pilots')->name('pilots')->middleware('can:activePilot')->group(function () {
   Route::prefix('rcl')->name('.rcl')->controller(RclMessagesController::class)->group(function () {
       Route::get('/', 'index')->name('.index');
       Route::get('/create', 'create')->name('.create');
       Route::post('/create', 'store')->name('.store');
   });

   Route::view('message-history', 'pilots.message-history')->name('.message-history');
});

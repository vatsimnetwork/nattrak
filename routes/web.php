<?php

use App\Http\Controllers\AdministrationController;
use App\Http\Controllers\BulletinsController;
use App\Http\Controllers\ClxMessagesController;
use App\Http\Controllers\RclMessagesController;
use App\Http\Controllers\TracksController;
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

Route::get('/', [\App\Http\Controllers\ViewsController::class, 'welcome'])->name('welcome');
Route::view('/about', 'about')->name('about');

Route::prefix('auth')->name('auth')->group(function () {
    Route::get('/redirect', [VatsimAuthController::class, 'redirect'])->name('.redirect');
    Route::get('/authenticate', [VatsimAuthController::class, 'authenticate'])->name('.authenticate');
    Route::get('/deauthenticate', [VatsimAuthController::class, 'deauthenticate'])->name('.deauthenticate');
    if (config('app.env') == 'local') {
        Route::get('/{cid}', function ($cid) {
            \Illuminate\Support\Facades\Auth::loginUsingId($cid);
            flashAlert(type: 'info', title: 'Signed in', message: 'Dev mode as '.$cid, toast: true, timer: true);

            return redirect()->route('welcome');
        });
    }
});

Route::prefix('administration')->name('administration')->middleware('can:administrate')->group(function () {
    Route::get('/', [AdministrationController::class, 'index'])->name('index');
    Route::get('/accounts', [AdministrationController::class, 'accounts'])->name('.accounts');
    Route::post('/accounts/add-access', [AdministrationController::class, 'addAccess'])->name('.accounts.add-access');
    Route::post('/accounts/remove-access', [AdministrationController::class, 'removeAccess'])->name('.accounts.remove-access');

    Route::get('/controllers', [AdministrationController::class, 'controllers'])->name('.controllers');
    Route::post('/controllers/add-access', [AdministrationController::class, 'addControllerAccess'])->name('.controllers.add-access');
    Route::post('/controllers/remove-access', [AdministrationController::class, 'removeControllerAccess'])->name('.controllers.remove-access');

    Route::get('/activity-log', [AdministrationController::class, 'activityLog'])->name('.activity-log');

    Route::get('/utility', [AdministrationController::class, 'utility'])->name('.utility');
    Route::post('/utility/clear', [AdministrationController::class, 'clearDb'])->name('.clear-db');
});

Route::prefix('pilots')->name('pilots')->middleware('can:activePilot')->group(function () {
    Route::prefix('rcl')->name('.rcl')->controller(RclMessagesController::class)->group(function () {
        Route::get('/', function () {
            return redirect(status: 301)->route('pilots.rcl.create');
        })->name('.index');
        Route::get('/create', 'create')->name('.create');
        Route::post('/create', 'store')->name('.store');
    });

    Route::get('message-history', function () {
        return view('pilots.message-history', [
            '_pageTitle' => 'Message History',
        ]);
    })->name('.message-history');

    Route::get('notify-new-eta', function () {
        return view('pilots.notify-new-eta', [
            '_pageTitle' => 'Notify New ETA',
        ]);
    })->name('.notify-new-eta');
});

Route::prefix('controllers')->name('controllers')->middleware('can:activeController')->group(function () {
    Route::prefix('clx')->name('.clx')->controller(ClxMessagesController::class)->group(function () {
        Route::get('/pending', 'getPending')->name('.pending');
        Route::get('/processed-dt', 'getProcessedViaClxModels')->name('.processed-dt');
        Route::get('/processed', 'getProcessed')->name('.processed');
        Route::get('/create', 'create')->name('.create');
        Route::post('/transmit/{rclMessage:id}', 'transmit')->name('.transmit');
        Route::get('/clx-msg/{clxMessage:id}', 'showClxMessage')->name('.show-clx-message');
        Route::get('/rcl-msg/{rclMessage:id}', 'showRclMessage')->name('.show-rcl-message');
        Route::post('/rcl-msg/{rclMessage:id}/del', 'deleteRclMessage')->name('.delete-rcl-message');
        Route::post('/rcl-msg/{rclMessage:id}/revert-to-voice', 'revertToVoice')->name('.revert-to-voice');
        Route::post('/rcl-msg/{rclMessage:id}/move-to-processed', 'moveToProcessed')->name('.move-to-processed');
    });
});

Route::prefix('domestic')->name('domestic')->middleware('can:activeBoundaryController')->group(function () {
   Route::get('notify-new-eta-for-pilot', function (){
       return view ('controllers.notify-new-eta-for-pilot', [
           '_pageTitle' => 'Notify New ETA for Pilot',
       ]);
   })->name('.notify-new-eta-for-pilot');
});

Route::prefix('notams')->name('notams')->controller(BulletinsController::class)->middleware('can:administrate')->group(function () {
    Route::get('/', 'index')->name('.index');
    Route::get('/create', 'create')->name('.create');
    Route::post('/', 'store')->name('.store');
    Route::delete('/{bulletin:id}', 'destroy')->name('.destroy');
});

Route::prefix('tracks')->name('tracks')->controller(TracksController::class)->group(function () {
    Route::get('/', 'index')->name('.index');
});

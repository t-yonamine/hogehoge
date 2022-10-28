<?php

use App\Http\Controllers\Back\EffectMeasurementController;
use App\Http\Controllers\operation\SchoolDrivingController;
use App\Http\Controllers\operation\AccountsController;
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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/home', function () {
    return view('home');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::group(
    ['middleware' => 'auth'],
    function () {

        // スタッフ管理
        Route::prefix('staff')->group(function () {
            Route::get('/', \App\Http\Action\Back\Staff\StaffIndexAction::class)->name('staff.index');
            // Route::get('/create', \App\Http\Action\Back\Staff\StaffCreateAction::class)->name('staff.create');
            // Route::get('/{id}', \App\Http\Action\Back\Staff\StaffEditAction::class)->name('school.edit');
            // Route::post('/store', \App\Http\Action\Back\Staff\StaffStoreAction::class)->name('staff.store');
            // Route::post('/update', \App\Http\Action\Back\Staff\StaffUpdateAction::class)->name('staff.update');
        });

        Route::controller(EffectMeasurementController::class)->prefix('effect-measurement')->name('effect-measurement.')->group(function () {
            Route::get('/{ledger_id}', 'index')->name('index');
            Route::delete('{ledger_id}', 'delete')->name('delete');
            Route::get('/create/{ledger_id}', 'create')->name('create');
            Route::post('/create', 'store')->name('store');
        });
    }
);
// admin : ログインユーザーが運営側ユーザーである && 役割がシステム管理者、または担当者を含むことを確認
Route::middleware(['auth', 'admin'])->group(
    function () {
        // school driving
        Route::controller(SchoolDrivingController::class)
            ->prefix('school-driving')->name('school-driving.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/create', 'create')->name('create');
                Route::post('/create', 'store')->name('store');
                Route::delete('/{id}', 'delete')->name('delete');
                Route::get('/{id}', 'detail')->name('detail');
                Route::put('/', 'edit')->name('edit');
            });
    }
);
// sys-admin: ログインユーザーが運営システム管理者であることを確認運営システム管理者でない場合、
Route::middleware(['auth', 'sys-admin'])->group(
    function () {
        Route::controller(AccountsController::class)->prefix('accounts')->name('accounts.')->group(function () {
            Route::get('/{id}', 'show')->name('show');
            Route::post('/{id}', 'update')->name('update');
            Route::get('/', 'index')->name('index');
            Route::delete('/{id}', 'delete')->name('delete');
        });
    }
);
require __DIR__ . '/auth.php';

<?php

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

Route::group(['middleware' => 'auth'], function () {

    // スタッフ管理
    Route::prefix('staff')->group(function () {
        Route::get('/', \App\Http\Action\Back\Staff\StaffIndexAction::class)->name('staff.index');
        // Route::get('/create', \App\Http\Action\Back\Staff\StaffCreateAction::class)->name('staff.create');
        // Route::get('/{id}', \App\Http\Action\Back\Staff\StaffEditAction::class)->name('school.edit');
        // Route::post('/store', \App\Http\Action\Back\Staff\StaffStoreAction::class)->name('staff.store');
        // Route::post('/update', \App\Http\Action\Back\Staff\StaffUpdateAction::class)->name('staff.update');
    });
    }
);

require __DIR__.'/auth.php';

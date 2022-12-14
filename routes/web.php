<?php

use App\Http\Controllers\Back\AptitudeDrivingController;
use App\Http\Controllers\Back\ApplicationTestController;
use App\Http\Controllers\Back\EffectMeasurementController;
use App\Http\Controllers\Back\SchoolStaffController;
use App\Http\Controllers\Back\StudentController;
use App\Http\Controllers\Front\HomeController;
use App\Http\Controllers\Front\StudentController as FrontStudentController;
use App\Http\Controllers\Front\TodayController;
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
        Route::controller(SchoolStaffController::class)->prefix('school-staff')->name('school-staff.')->group(function () {
            Route::get('/create', 'create')->name('create');
            Route::post('/create', 'store')->name('store');
            Route::get('/{id}', 'show')->name('show');
            Route::put('/{id}', 'update')->name('update');
            Route::get('/', 'index')->name('index');
            Route::delete('/{id}', 'delete')->name('delete');
        });

        Route::controller(EffectMeasurementController::class)->prefix('effect-measurement')->name('effect-measurement.')->group(function () {
            Route::get('/import', 'import')->name('import');
            Route::post('/import', 'upload')->name('import.upload');
            Route::post('/insert', 'insert')->name('import.insert');
            Route::get('/{ledger_id}', 'index')->name('index');
            Route::delete('{ledger_id}', 'delete')->name('delete');
            Route::get('/create/{ledger_id}', 'create')->name('create');
            Route::post('/create', 'store')->name('store');
        });

        Route::controller(AptitudeDrivingController::class)->prefix('aptitude-driving')->name('aptitude-driving.')->group(function () {
            Route::get('/edit/{aptitude_drv_id}', 'detail')->name('detail');
            Route::post('/edit/{aptitude_drv_id}', 'edit')->name('edit');
            Route::delete('/edit/{aptitude_drv_id}', 'delete')->name('delete');
            Route::get('/create', 'create')->name('create');
            Route::post('/create', 'store')->name('store');
            Route::get('/import', 'importFile')->name('importFile');
            Route::post('/import', 'upload')->name('import.upload');
            Route::post('/insert', 'insert')->name('import.insert');
        });

        Route::controller(StudentController::class)->prefix('student')->name('student.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{id}', 'detail')->name('detail');
            Route::get('/update/{id}', 'show')->name('show');
            Route::post('/update/{id}', 'update')->name('update');
        });

        // ?????????????????? 
        Route::controller(ApplicationTestController::class)->prefix('apply-test')->name('apply-test.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::post('/{id}', 'post')->name('post');
            Route::get('/{lesson_attend_id}', 'completionTest')->name('completion');
            Route::get('examiner-allocation-regis/ajax', 'examinerAllocationRegisAjax')->name('examiner-allocation-regis.ajax');
            Route::post('examiner-allocation-regis/ajax-save', 'examinerAllocationRegisAjaxSave')->name('examiner-allocation-regis.ajax-save')->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
            Route::post('error-page', 'errorPage')->name('error-page');
            Route::get('/ledger/create', 'create')->name('create');
            Route::post('/ledger/create', 'store')->name('store');
        });
    }
);
// admin : ????????????????????????????????????????????????????????? && ???????????????????????????????????????????????????????????????????????????
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
// sys-admin: ???????????????????????????????????????????????????????????????????????????????????????????????????????????????????????????
Route::middleware(['auth', 'sys-admin'])->group(
    function () {
        Route::controller(AccountsController::class)->prefix('accounts')->name('accounts.')->group(function () {
            Route::get('/create', 'create')->name('create');
            Route::post('/create', 'store')->name('store');
            Route::get('/{id}', 'show')->name('show');
            Route::post('/{id}', 'update')->name('update');
            Route::get('/', 'index')->name('index');
            Route::delete('/{id}', 'delete')->name('delete');
        });
    }
);
Route::middleware('auth')->prefix('frt')->name('frt.')->group(
    function () {
        Route::controller(HomeController::class)->group(function () {
            Route::get('/home', 'index')->name('index');
            Route::post('/home', 'date')->name('date');

            // ???????????? (today)
        });
        Route::controller(TodayController::class)->prefix('/today')->name('today.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::put('/', 'update')->name('update');
            Route::post('/comment', 'commentSave')->name('comment');
            Route::post('/new-period', 'newPeriod')->name('newPeriod');
        });
        Route::controller(FrontStudentController::class)->prefix('/student')->name('student.')->group(function () {
            Route::get('/{ledger_id}', 'detail')->name('detail');
        });
    }
);

require __DIR__ . '/auth.php';

<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoogleSheetController;
use App\Http\Controllers\TrellController;
use Illuminate\Support\Facades\Artisan;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Str;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/update-card', function () {
    Artisan::call('update:trello-card');
});



// Route::get('/', [DashboardController::class, 'ShowDashboard'])->name('dashboard');

Route::get('/fetch-data', [DashboardController::class, 'FetchLists'])->name('fetchData');
Route::get('/export', [DashboardController::class, 'export'])->name('export');

// Route::get('/accessSheet', [GoogleSheetController::class, 'index'])->name('accessSheet');

Route::get('logs', [LogViewerController::class, 'index']);

Route::get('reset-all', function () {
    Artisan::call('migrate:fresh --seed');
});

Route::post('/update/board', [WebhookController::class, 'updateBoard']);

// This route will help us to create webhook for trello
Route::match(['head'], '/update/board', [WebhookController::class, 'create_webhook']);
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GoogleSheetController;
use Rap2hpoutre\LaravelLogViewer\LogViewerController;

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

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [DashboardController::class, 'ShowDashboard'])->name('dashboard');

Route::post('/fetch-data', [DashboardController::class, 'FetchLists'])->name('fetchData');

Route::get('/accessSheet', [GoogleSheetController::class, 'index'])->name('accessSheet');

Route::get('logs', [LogViewerController::class, 'index']);
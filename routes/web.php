<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ReportMapController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::resource('report', ReportController::class);

Route::post('/report/analyze', [ReportController::class, 'analyze'])->name('report.analyze');

Route::get('/reports', [ReportController::class, 'list'])->name('report.list');

Route::get('/users', [UserController::class, 'index'])->name('users.index')->middleware('auth');

require __DIR__.'/auth.php';

// 既存のルート定義の後に追加
Route::resource('users', UserController::class)->middleware('auth');

Route::get('/report/{report}/edit', [ReportController::class, 'edit'])->name('report.edit');
Route::put('/report/{report}', [ReportController::class, 'update'])->name('report.update');
Route::delete('/report/{report}', [ReportController::class, 'destroy'])->name('report.destroy');

Route::get('/report-map', [ReportController::class, 'index'])->name('report.map')->middleware('auth');
Route::get('/api/reports', [ReportController::class, 'getReports']); // JSONデータを取得するルート
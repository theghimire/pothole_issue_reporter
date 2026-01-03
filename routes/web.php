<?php

use App\Http\Controllers\PotholeController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;

// --- Language Switcher ---
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ne'])) {
        session(['locale' => $locale]);
    }
    return back();
})->name('lang.switch');

// --- Citizen/Public Routes ---
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/report', [PotholeController::class, 'index'])->name('report');
Route::post('/report', [PotholeController::class, 'store'])->name('report.store');
Route::get('/success/{ticket_id}', [PotholeController::class, 'success'])->name('report.success');
Route::get('/track', [PotholeController::class, 'track'])->name('track');
Route::post('/track', [PotholeController::class, 'checkStatus'])->name('track.check');
Route::get('/report/density-check', [PotholeController::class, 'checkDensity'])->name('report.density');

// Admin Side
Route::get('/admin/login', [AdminController::class, 'loginForm'])->name('admin.login');
Route::post('/admin/login', [AdminController::class, 'login'])->name('admin.login.submit');
Route::get('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');
Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
Route::post('/admin/report/{id}/status', [AdminController::class, 'updateStatus'])->name('admin.status.update');
Route::delete('/admin/report/{id}', [AdminController::class, 'destroy'])->name('admin.report.destroy');
Route::get('/admin/export-csv', [AdminController::class, 'exportCSV'])->name('admin.export.csv');
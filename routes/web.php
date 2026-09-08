<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropertyController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/financials/update', [DashboardController::class, 'updateFinancials'])->name('financials.update');

    Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('properties.show');
    Route::get('/properties/{property}/export-pdf', [PropertyController::class, 'exportPdf'])->name('properties.export-pdf');
    Route::post('/properties/{property}/assets', [PropertyController::class, 'storeAsset'])->name('properties.assets.store');
    Route::put('/properties/{property}/assets/{asset}', [PropertyController::class, 'updateAsset'])->name('properties.assets.update');
    Route::delete('/properties/{property}/assets/{asset}', [PropertyController::class, 'destroyAsset'])->name('properties.assets.destroy');
    Route::post('/properties/{property}/maintenance-logs', [PropertyController::class, 'storeMaintenanceLog'])->name('properties.maintenance_logs.store');
    Route::put('/properties/{property}/maintenance-logs/{maintenanceLog}', [PropertyController::class, 'updateMaintenanceLog'])->name('properties.maintenance_logs.update');
    Route::delete('/properties/{property}/maintenance-logs/{maintenanceLog}', [PropertyController::class, 'destroyMaintenanceLog'])->name('properties.maintenance_logs.destroy');
    Route::post('/properties', [PropertyController::class, 'store'])->name('properties.store');
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
<?php

use App\Http\Controllers\Owner\DashboardController;
use App\Http\Controllers\Owner\PropertyController;
use App\Http\Controllers\Owner\RenterController;
use App\Http\Controllers\Owner\TransactionController;
use App\Http\Controllers\Owner\UnitController;
use Illuminate\Support\Facades\Route;

Route::prefix('owner')
    ->middleware(['auth', 'owner'])
    ->group(function (): void {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('owner.dashboard');
        Route::resource('properties', PropertyController::class);
        Route::resource('units', UnitController::class);
        Route::resource('renters', RenterController::class);
        Route::resource('transactions', TransactionController::class);
    });

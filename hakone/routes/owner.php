<?php

use App\Http\Controllers\Owner\DashboardController;
use App\Http\Controllers\Owner\PropertyController;
use App\Http\Controllers\Owner\UnitController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'owner'])
    ->prefix('owner')
    ->name('owner.')
    ->group(function (): void {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        Route::resource('properties', PropertyController::class);
        Route::resource('properties.units', UnitController::class)
            ->except(['show']);
    });

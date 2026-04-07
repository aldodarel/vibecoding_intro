<?php

use App\Http\Controllers\Renter\DashboardController;
use App\Http\Controllers\Renter\PaymentController;
use Illuminate\Support\Facades\Route;

Route::prefix('renter')
    ->middleware(['auth', 'renter'])
    ->group(function (): void {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('renter.dashboard');
        Route::get('payments', [PaymentController::class, 'index'])->name('renter.payments.index');
        Route::get('payments/{id}', [PaymentController::class, 'show'])->name('renter.payments.show');
    });

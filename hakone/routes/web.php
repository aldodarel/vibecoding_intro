<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');

require __DIR__ . '/auth.php';
require __DIR__ . '/owner.php';
require __DIR__ . '/renter.php';

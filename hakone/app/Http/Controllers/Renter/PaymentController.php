<?php

namespace App\Http\Controllers\Renter;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;

class PaymentController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('renter/payments/Index');
    }

    public function show(string $id): Response
    {
        return Inertia::render('renter/payments/Show', [
            'id' => $id,
        ]);
    }
}

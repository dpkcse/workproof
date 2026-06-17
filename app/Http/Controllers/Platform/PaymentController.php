<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __invoke(): View
    {
        return view('platform.payments.index', [
            'paymentsTableExists' => Schema::hasTable('payments'),
            'payments' => Schema::hasTable('payments') ? DB::table('payments')->latest()->limit(50)->get() : collect(),
        ]);
    }
}

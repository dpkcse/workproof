<?php
namespace App\Http\Controllers\Platform;use App\Http\Controllers\Controller;use App\Models\Invoice;class BillingController extends Controller{public function invoices(){return view('platform.invoices.index',['invoices'=>Invoice::with('workspace')->latest()->paginate(30)]);}}

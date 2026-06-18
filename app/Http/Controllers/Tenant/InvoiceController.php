<?php
namespace App\Http\Controllers\Tenant;use App\Http\Controllers\Controller;use App\Models\Invoice;use App\Support\CurrentWorkspace;
class InvoiceController extends Controller{use Concerns;public function index(CurrentWorkspace $cw){return view('tenant.billing.invoices.index',['invoices'=>$this->ws($cw)->invoices()->latest()->paginate(20)]);}public function show(CurrentWorkspace $cw,Invoice $invoice){abort_unless($invoice->workspace_id===$this->ws($cw)->id,404);return view('tenant.billing.invoices.show',['invoice'=>$invoice->load('items','payments')]);}}

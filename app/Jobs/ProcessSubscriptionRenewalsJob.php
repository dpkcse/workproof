<?php
namespace App\Jobs;use App\Models\Subscription;use App\Services\Billing\BillingService;use Illuminate\Contracts\Queue\ShouldQueue;use Illuminate\Foundation\Queue\Queueable;
class ProcessSubscriptionRenewalsJob implements ShouldQueue{use Queueable;public function handle(BillingService $billing):void{Subscription::whereIn('status',['active','trial'])->whereDate('current_period_end','<=',today()->addDay())->chunkById(100,function($subs)use($billing){foreach($subs as $s){if(!$s->invoices()->whereDate('issue_date',today())->exists())$billing->createInvoiceForSubscription($s);}});}}

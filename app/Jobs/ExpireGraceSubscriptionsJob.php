<?php
namespace App\Jobs;use App\Models\Subscription;use App\Services\Billing\SubscriptionService;use Illuminate\Contracts\Queue\ShouldQueue;use Illuminate\Foundation\Queue\Queueable;
class ExpireGraceSubscriptionsJob implements ShouldQueue{use Queueable;public function handle(SubscriptionService $service):void{Subscription::where('status','past_due')->whereDate('grace_ends_at','<',today())->chunkById(100,fn($subs)=>$subs->each(fn($s)=>$service->suspendExpiredSubscription($s)));}}

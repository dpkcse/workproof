<?php
namespace App\Jobs;use App\Models\Subscription;use App\Notifications\SubscriptionRenewalReminderNotification;use Illuminate\Contracts\Queue\ShouldQueue;use Illuminate\Foundation\Queue\Queueable;
class SendSubscriptionRenewalReminderJob implements ShouldQueue{use Queueable;public function handle():void{$date=today()->addDays(config('billing.renewal_reminder_days',7));Subscription::whereIn('status',['active','trial'])->whereDate('current_period_end',$date)->chunkById(100,fn($subs)=>$subs->each(fn($s)=>$s->workspace?->owner?->notify(new SubscriptionRenewalReminderNotification($s))));}}

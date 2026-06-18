<?php
namespace App\Notifications;use Illuminate\Bus\Queueable;use Illuminate\Notifications\Notification;
class AiSummaryFailedNotification extends Notification{use Queueable;public function __construct(public string $feature,public string $message){}public function via(object $notifiable):array{return ['database'];}public function toArray(object $notifiable):array{return ['feature'=>$this->feature,'message'=>$this->message];}}

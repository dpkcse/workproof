<?php
namespace App\Notifications;use Illuminate\Bus\Queueable;use Illuminate\Notifications\Messages\MailMessage;use Illuminate\Notifications\Notification;
class UserInvitedToWorkspaceNotification extends Notification{use Queueable;public function via(object $notifiable):array{return ['database'];}public function toArray(object $notifiable):array{return ['type'=>'UserInvitedToWorkspaceNotification'];}public function toMail(object $notifiable):MailMessage{return (new MailMessage)->subject('NAXAS WorkProof Notification')->line('You have a NAXAS WorkProof notification.');}}

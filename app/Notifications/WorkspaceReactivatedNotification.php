<?php

namespace App\Notifications;

use App\Models\Workspace;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkspaceReactivatedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly ?Workspace $workspace = null)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'workspace_reactivated',
            'workspace_id' => $this->workspace?->id,
            'workspace_name' => $this->workspace?->name,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Workspace reactivated')
            ->line('Your NAXAS WorkProof workspace has been reactivated.');
    }
}

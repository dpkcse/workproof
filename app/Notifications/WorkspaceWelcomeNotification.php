<?php

namespace App\Notifications;

use App\Models\Workspace;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkspaceWelcomeNotification extends Notification
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
            'type' => 'workspace_welcome',
            'workspace_id' => $this->workspace?->id,
            'workspace_name' => $this->workspace?->name,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Welcome to NAXAS WorkProof')
            ->line('Your NAXAS WorkProof workspace is ready.');
    }
}

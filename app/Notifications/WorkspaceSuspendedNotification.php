<?php

namespace App\Notifications;

use App\Models\Workspace;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkspaceSuspendedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly ?Workspace $workspace = null, private readonly ?string $reason = null)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'workspace_suspended',
            'workspace_id' => $this->workspace?->id,
            'workspace_name' => $this->workspace?->name,
            'reason' => $this->reason,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Workspace suspended')
            ->line('Your NAXAS WorkProof workspace is currently suspended.')
            ->line('Please contact the workspace owner or platform support.');
    }
}

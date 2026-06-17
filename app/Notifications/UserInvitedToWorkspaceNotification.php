<?php

namespace App\Notifications;

use App\Models\Workspace;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserInvitedToWorkspaceNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly ?Workspace $workspace = null, private readonly ?string $role = null)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'workspace_invitation',
            'workspace_id' => $this->workspace?->id,
            'workspace_name' => $this->workspace?->name,
            'role' => $this->role,
            'todo' => 'Secure invitation token flow will be implemented in a later phase.',
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('You were invited to NAXAS WorkProof')
            ->line('You have been invited to a workspace. Secure invitation links will be added in a later phase.');
    }
}

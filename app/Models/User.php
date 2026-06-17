<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'status',
        'is_platform_user',
        'default_timezone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_platform_user' => 'boolean',
        ];
    }

    public function workspaces()
    {
        return $this->belongsToMany(Workspace::class)
            ->withPivot(['role_key', 'status', 'invited_by', 'joined_at'])
            ->withTimestamps();
    }

    public function ownedWorkspaces()
    {
        return $this->hasMany(Workspace::class, 'owner_id');
    }

    public function belongsToWorkspace(int|string $workspaceId): bool
    {
        return $this->workspaces()
            ->where('workspaces.id', $workspaceId)
            ->exists();
    }

    public function activeWorkspaceMembership(int|string $workspaceId): ?object
    {
        return $this->workspaces()
            ->where('workspaces.id', $workspaceId)
            ->wherePivot('status', 'active')
            ->first()?->pivot;
    }

    public function isPlatformUser(): bool
    {
        return (bool) $this->is_platform_user;
    }

    public function canInWorkspace(string $permission, Workspace $workspace): bool
    {
        if (! $this->activeWorkspaceMembership($workspace->id)) {
            return false;
        }

        return method_exists($this, 'can') ? $this->can($permission) : false;
    }
}

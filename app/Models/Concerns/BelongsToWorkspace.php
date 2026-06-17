<?php

namespace App\Models\Concerns;

use App\Models\Workspace;
use App\Support\CurrentWorkspace;
use Illuminate\Database\Eloquent\Builder;

trait BelongsToWorkspace
{
    public const WORKSPACE_SCOPE = 'current_workspace';

    protected static function bootBelongsToWorkspace(): void
    {
        static::creating(function ($model): void {
            $currentWorkspace = app(CurrentWorkspace::class);

            if (empty($model->workspace_id) && $currentWorkspace->isResolved()) {
                $model->workspace_id = $currentWorkspace->id();
            }
        });

        static::addGlobalScope(self::WORKSPACE_SCOPE, function (Builder $builder): void {
            $currentWorkspace = app(CurrentWorkspace::class);

            if ($currentWorkspace->isResolved()) {
                $builder->where(
                    $builder->getModel()->getTable().'.workspace_id',
                    $currentWorkspace->id()
                );
            }
        });
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }

    public function scopeForWorkspace(Builder $query, int|string $workspaceId): Builder
    {
        return $query->withoutGlobalScope(self::WORKSPACE_SCOPE)
            ->where($query->getModel()->getTable().'.workspace_id', $workspaceId);
    }

    public function scopeWithoutWorkspaceScope(Builder $query): Builder
    {
        return $query->withoutGlobalScope(self::WORKSPACE_SCOPE);
    }
}

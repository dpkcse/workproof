<?php

namespace App\Support;

use App\Models\Workspace;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CurrentWorkspace
{
    private ?Workspace $workspace = null;

    public function set(?Workspace $workspace): void
    {
        $this->workspace = $workspace;
    }

    public function get(): ?Workspace
    {
        return $this->workspace;
    }

    public function id(): ?int
    {
        return $this->workspace?->id;
    }

    public function required(): Workspace
    {
        if (! $this->workspace) {
            throw new AccessDeniedHttpException('A workspace context is required to access this area.');
        }

        return $this->workspace;
    }

    public function clear(): void
    {
        $this->workspace = null;
    }

    public function isResolved(): bool
    {
        return $this->workspace !== null;
    }
}

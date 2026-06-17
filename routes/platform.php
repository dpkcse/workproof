<?php

use App\Models\Workspace;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'ensure.platform.user'])
    ->prefix('platform')
    ->name('platform.')
    ->group(function (): void {
        Route::view('/dashboard', 'platform.dashboard')->name('dashboard');
        Route::get('/workspaces', fn () => view('platform.workspaces.index', [
            'workspaces' => Workspace::query()->latest()->paginate(20),
        ]))->name('workspaces.index');
        Route::get('/workspaces/{workspace}', fn (Workspace $workspace) => view('platform.workspaces.show', [
            'workspace' => $workspace,
        ]))->name('workspaces.show');
        Route::post('/workspaces/{workspace}/suspend', function (Workspace $workspace) {
            $workspace->forceFill([
                'status' => 'suspended',
                'suspended_at' => now(),
                'suspended_by' => auth()->id(),
            ])->save();

            return back();
        })->name('workspaces.suspend');
        Route::post('/workspaces/{workspace}/reactivate', function (Workspace $workspace) {
            $workspace->forceFill([
                'status' => 'active',
                'suspended_at' => null,
                'suspended_by' => null,
                'suspension_reason' => null,
            ])->save();

            return back();
        })->name('workspaces.reactivate');
    });

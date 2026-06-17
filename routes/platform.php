<?php

use App\Http\Controllers\Platform\AiUsageController;
use App\Http\Controllers\Platform\DashboardController;
use App\Http\Controllers\Platform\PaymentController;
use App\Http\Controllers\Platform\PlanController;
use App\Http\Controllers\Platform\SubscriptionController;
use App\Http\Controllers\Platform\SupportController;
use App\Http\Controllers\Platform\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'ensure.platform.user'])
    ->prefix('platform')
    ->name('platform.')
    ->group(function (): void {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');

        Route::get('/workspaces', [WorkspaceController::class, 'index'])->name('workspaces.index');
        Route::get('/workspaces/{workspace}', [WorkspaceController::class, 'show'])->name('workspaces.show');
        Route::post('/workspaces/{workspace}/suspend', [WorkspaceController::class, 'suspend'])->name('workspaces.suspend');
        Route::post('/workspaces/{workspace}/reactivate', [WorkspaceController::class, 'reactivate'])->name('workspaces.reactivate');

        Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');
        Route::post('/plans', [PlanController::class, 'store'])->name('plans.store');

        Route::get('/subscriptions', SubscriptionController::class)->name('subscriptions.index');
        Route::get('/payments', PaymentController::class)->name('payments.index');
        Route::get('/ai-usage', AiUsageController::class)->name('ai-usage.index');
        Route::get('/support', SupportController::class)->name('support.index');
    });

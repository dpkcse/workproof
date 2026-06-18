<?php

use App\Http\Controllers\Platform\AiUsageController;
use App\Http\Controllers\Platform\SystemHealthController;
use App\Http\Controllers\Platform\StorageUsageController;
use App\Http\Controllers\Platform\BillingController;
use App\Http\Controllers\Platform\DashboardController;
use App\Http\Controllers\Platform\PaymentController;
use App\Http\Controllers\Platform\PlanController;
use App\Http\Controllers\Platform\SubscriptionController;
use App\Http\Controllers\Platform\SupportController;
use App\Http\Controllers\Platform\WorkspaceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'ensure.platform.user'])
    ->name('platform.')
    ->group(function (): void {
        Route::get('/dashboard', DashboardController::class)->name('dashboard');

        Route::get('/workspaces', [WorkspaceController::class, 'index'])->name('workspaces.index');
        Route::get('/workspaces/{workspace}', [WorkspaceController::class, 'show'])->name('workspaces.show');
        Route::post('/workspaces/{workspace}/suspend', [WorkspaceController::class, 'suspend'])->name('workspaces.suspend');
        Route::post('/workspaces/{workspace}/reactivate', [WorkspaceController::class, 'reactivate'])->name('workspaces.reactivate');

        Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');
        Route::post('/plans', [PlanController::class, 'store'])->name('plans.store');

        Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::post('/subscriptions/{subscription}/change-status', [SubscriptionController::class, 'changeStatus'])->name('subscriptions.change-status');
        Route::get('/invoices', [BillingController::class, 'invoices'])->name('invoices.index');
        Route::post('/invoices/{invoice}/mark-paid', [PaymentController::class, 'markInvoicePaid'])->name('invoices.mark-paid');
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::post('/payments/{payment}/mark-successful', [PaymentController::class, 'markSuccessful'])->name('payments.mark-successful');
        Route::get('/ai-usage', AiUsageController::class)->name('ai-usage.index');
        Route::get('/system-health', SystemHealthController::class)->name('system-health');
        Route::get('/storage-usage', StorageUsageController::class)->name('storage-usage');
        Route::get('/support', SupportController::class)->name('support.index');
    });

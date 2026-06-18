<?php

use App\Http\Controllers\Tenant\CompanySettingsController;
use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Tenant\OnboardingController;
use App\Http\Controllers\Tenant\TaskController;
use App\Http\Controllers\Tenant\TaskCommentController;
use App\Http\Controllers\Tenant\TaskChecklistController;
use App\Http\Controllers\Tenant\TaskAttachmentController;
use App\Http\Controllers\Tenant\ProjectController;
use App\Http\Controllers\Tenant\TaskCategoryController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Tenant\DailyReportController;
use App\Http\Controllers\Tenant\DailyReportReviewController;
use App\Http\Controllers\Tenant\WorkProofController;
use App\Http\Controllers\Tenant\TaskApprovalController;
use App\Http\Controllers\Tenant\ManagerDashboardController;
use App\Http\Controllers\Tenant\MissingReportController;
use App\Http\Controllers\Tenant\AiController;
use App\Http\Controllers\Tenant\AiSummaryController;


Route::middleware([
    'auth',
    'resolve.current.workspace',
    'ensure.workspace.active',
    'ensure.tenant.member',
])->group(function (): void {
    Route::get('/dashboard', DashboardController::class)->name('tenant.dashboard');

    Route::get('/onboarding', [OnboardingController::class, 'index'])->name('tenant.onboarding');
    Route::post('/onboarding/company-profile', [OnboardingController::class, 'companyProfile'])->name('tenant.onboarding.company-profile');
    Route::post('/onboarding/departments', [OnboardingController::class, 'departments'])->name('tenant.onboarding.departments');
    Route::post('/onboarding/invite-users', [OnboardingController::class, 'inviteUsers'])->name('tenant.onboarding.invite-users');
    Route::post('/onboarding/policies', [OnboardingController::class, 'policies'])->name('tenant.onboarding.policies');
    Route::post('/onboarding/ai-settings', [OnboardingController::class, 'aiSettings'])->name('tenant.onboarding.ai-settings');
    Route::post('/onboarding/complete', [OnboardingController::class, 'complete'])->name('tenant.onboarding.complete');

    Route::get('/settings/company', [CompanySettingsController::class, 'edit'])->name('tenant.settings.company');
    Route::post('/settings/company', [CompanySettingsController::class, 'update'])->name('tenant.settings.company.update');



    Route::get('/daily-reports/review', [DailyReportReviewController::class, 'index'])->name('tenant.daily-reports.review.index');
    Route::get('/daily-reports/{dailyReport}/review', [DailyReportReviewController::class, 'show'])->name('tenant.daily-reports.review.show');
    Route::post('/daily-reports/{dailyReport}/approve', [DailyReportReviewController::class, 'approve'])->name('tenant.daily-reports.approve');
    Route::post('/daily-reports/{dailyReport}/reject', [DailyReportReviewController::class, 'reject'])->name('tenant.daily-reports.reject');
    Route::get('/daily-reports', [DailyReportController::class, 'index'])->name('tenant.daily-reports.index');
    Route::get('/daily-reports/create', [DailyReportController::class, 'create'])->name('tenant.daily-reports.create');
    Route::post('/daily-reports', [DailyReportController::class, 'store'])->name('tenant.daily-reports.store');
    Route::get('/daily-reports/{dailyReport}', [DailyReportController::class, 'show'])->name('tenant.daily-reports.show');
    Route::get('/daily-reports/{dailyReport}/edit', [DailyReportController::class, 'edit'])->name('tenant.daily-reports.edit');
    Route::put('/daily-reports/{dailyReport}', [DailyReportController::class, 'update'])->name('tenant.daily-reports.update');
    Route::post('/daily-reports/{dailyReport}/submit', [DailyReportController::class, 'submit'])->name('tenant.daily-reports.submit');
    Route::post('/tasks/{task}/proofs', [WorkProofController::class, 'store'])->name('tenant.tasks.proofs.store');
    Route::get('/proof-review', [WorkProofController::class, 'index'])->name('tenant.proof-review.index');
    Route::post('/proofs/{proof}/approve', [WorkProofController::class, 'approve'])->name('tenant.proofs.approve');
    Route::post('/proofs/{proof}/reject', [WorkProofController::class, 'reject'])->name('tenant.proofs.reject');
    Route::post('/tasks/{task}/request-approval', [TaskApprovalController::class, 'request'])->name('tenant.tasks.request-approval');
    Route::get('/approvals', [TaskApprovalController::class, 'index'])->name('tenant.approvals.index');
    Route::post('/task-approvals/{approval}/approve', [TaskApprovalController::class, 'approve'])->name('tenant.task-approvals.approve');
    Route::post('/task-approvals/{approval}/reject', [TaskApprovalController::class, 'reject'])->name('tenant.task-approvals.reject');
    Route::get('/missing-reports', [MissingReportController::class, 'index'])->name('tenant.missing-reports.index');
    Route::get('/manager-dashboard', ManagerDashboardController::class)->name('tenant.manager-dashboard');
    Route::get('/ai-summary', AiSummaryController::class)->name('tenant.ai-summary.index');
    Route::post('/ai/daily-report-summary', [AiController::class, 'dailyReportSummary'])->name('tenant.ai.daily-report-summary');
    Route::post('/ai/task-cleaner', [AiController::class, 'taskCleaner'])->name('tenant.ai.task-cleaner');
    Route::post('/ai/checklist-generator', [AiController::class, 'checklistGenerator'])->name('tenant.ai.checklist-generator');
    Route::post('/ai/risk-summary', [AiController::class, 'riskSummary'])->name('tenant.ai.risk-summary');
    Route::post('/ai/follow-up-suggestion', [AiController::class, 'followUpSuggestion'])->name('tenant.ai.follow-up-suggestion');
    Route::post('/ai/weekly-summary', [AiController::class, 'weeklySummary'])->name('tenant.ai.weekly-summary');

    Route::get('/tasks', [TaskController::class, 'index'])->name('tenant.tasks.index');
    Route::get('/tasks/create', [TaskController::class, 'create'])->name('tenant.tasks.create');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tenant.tasks.store');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tenant.tasks.show');
    Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tenant.tasks.edit');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tenant.tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tenant.tasks.destroy');
    Route::post('/tasks/{task}/comments', [TaskCommentController::class, 'store'])->name('tenant.tasks.comments.store');
    Route::post('/tasks/{task}/checklists', [TaskChecklistController::class, 'store'])->name('tenant.tasks.checklists.store');
    Route::post('/tasks/{task}/ai-checklist/accept', [AiController::class, 'acceptChecklist'])->name('tenant.tasks.ai-checklist.accept');
    Route::patch('/tasks/{task}/checklists/{checklist}', [TaskChecklistController::class, 'update'])->name('tenant.tasks.checklists.update');
    Route::delete('/tasks/{task}/checklists/{checklist}', [TaskChecklistController::class, 'destroy'])->name('tenant.tasks.checklists.destroy');
    Route::post('/tasks/{task}/attachments', [TaskAttachmentController::class, 'store'])->name('tenant.tasks.attachments.store');
    Route::delete('/tasks/{task}/attachments/{attachment}', [TaskAttachmentController::class, 'destroy'])->name('tenant.tasks.attachments.destroy');
    Route::get('/projects', [ProjectController::class, 'index'])->name('tenant.projects.index');
    Route::post('/projects', [ProjectController::class, 'store'])->name('tenant.projects.store');
    Route::get('/task-categories', [TaskCategoryController::class, 'index'])->name('tenant.task-categories.index');
    Route::post('/task-categories', [TaskCategoryController::class, 'store'])->name('tenant.task-categories.store');

    Route::view('/users', 'tenant.placeholder')->name('tenant.users.index')->defaults('title', 'Users');
    Route::view('/teams', 'tenant.placeholder')->name('tenant.teams.index')->defaults('title', 'Teams');
    Route::view('/departments', 'tenant.placeholder')->name('tenant.departments.index')->defaults('title', 'Departments');
    Route::view('/billing', 'tenant.placeholder')->name('tenant.billing')->defaults('title', 'Billing');
});

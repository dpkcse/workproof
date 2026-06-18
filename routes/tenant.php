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


    Route::get('/tasks', [TaskController::class, 'index'])->name('tenant.tasks.index');
    Route::get('/tasks/create', [TaskController::class, 'create'])->name('tenant.tasks.create');
    Route::post('/tasks', [TaskController::class, 'store'])->name('tenant.tasks.store');
    Route::get('/tasks/{task}', [TaskController::class, 'show'])->name('tenant.tasks.show');
    Route::get('/tasks/{task}/edit', [TaskController::class, 'edit'])->name('tenant.tasks.edit');
    Route::put('/tasks/{task}', [TaskController::class, 'update'])->name('tenant.tasks.update');
    Route::delete('/tasks/{task}', [TaskController::class, 'destroy'])->name('tenant.tasks.destroy');
    Route::post('/tasks/{task}/comments', [TaskCommentController::class, 'store'])->name('tenant.tasks.comments.store');
    Route::post('/tasks/{task}/checklists', [TaskChecklistController::class, 'store'])->name('tenant.tasks.checklists.store');
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

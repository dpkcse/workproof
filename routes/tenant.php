<?php

use App\Http\Controllers\Tenant\CompanySettingsController;
use App\Http\Controllers\Tenant\DashboardController;
use App\Http\Controllers\Tenant\OnboardingController;
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

    Route::view('/users', 'tenant.placeholder')->name('tenant.users.index')->defaults('title', 'Users');
    Route::view('/teams', 'tenant.placeholder')->name('tenant.teams.index')->defaults('title', 'Teams');
    Route::view('/departments', 'tenant.placeholder')->name('tenant.departments.index')->defaults('title', 'Departments');
    Route::view('/billing', 'tenant.placeholder')->name('tenant.billing')->defaults('title', 'Billing');
});

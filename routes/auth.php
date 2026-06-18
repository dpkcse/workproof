<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\RegisteredWorkspaceController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('/login', [AuthenticatedSessionController::class, 'store']);
    Route::view('/forgot-password', 'auth.forgot-password')->name('password.request');
    Route::middleware('ensure.saas.enabled')->group(function (): void {
        Route::get('/register', [RegisteredWorkspaceController::class, 'create'])->name('register');
        Route::post('/register', [RegisteredWorkspaceController::class, 'store'])->name('register.store');
    });
});

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth')
    ->name('logout');

Route::view('/workspace/setup', 'auth.workspace-setup')
    ->middleware('auth')
    ->name('workspace.setup');

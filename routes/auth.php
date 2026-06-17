<?php

use App\Http\Controllers\Auth\RegisteredWorkspaceController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::view('/login', 'auth.login')->name('login');
    Route::get('/register', [RegisteredWorkspaceController::class, 'create'])->name('register');
    Route::post('/register', [RegisteredWorkspaceController::class, 'store'])->name('register.store');
});

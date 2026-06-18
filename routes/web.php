<?php

use App\Http\Controllers\Public\PricingController;
use Illuminate\Support\Facades\Route;

$mainDomain = config('workproof.domains.main');
$adminDomain = config('workproof.domains.admin');
$tenantSuffix = config('workproof.domains.tenant_suffix', config('workproof.domains.subdomain_root'));

$publicRoutes = function (): void {
    Route::view('/', 'welcome')->name('home');
    Route::view('/features', 'public.features')->name('features');
    Route::get('/pricing', PricingController::class)->name('pricing');
    Route::view('/use-cases/agencies', 'public.use-cases.agencies')->name('use-cases.agencies');
    Route::view('/use-cases/small-business', 'public.use-cases.small-business')->name('use-cases.small-business');
    Route::view('/contact', 'public.contact')->name('contact');
};

$authRoutes = function (): void {
    require __DIR__.'/auth.php';
};

Route::domain($mainDomain)->group(function () use ($publicRoutes, $authRoutes): void {
    $publicRoutes();
    $authRoutes();
});

Route::domain($adminDomain)->group(function () use ($authRoutes): void {
    $authRoutes();
    require __DIR__.'/platform.php';
});

Route::domain('{workspace}.'.$tenantSuffix)
    ->where(['workspace' => '^(?!admin$|www$)[a-z0-9][a-z0-9-]*[a-z0-9]$'])
    ->group(function () use ($authRoutes): void {
        $authRoutes();
        require __DIR__.'/tenant.php';
    });

if (app()->isLocal()) {
    Route::domain('127.0.0.1')->group(function () use ($publicRoutes, $authRoutes): void {
        $publicRoutes();
        $authRoutes();
    });

    Route::domain('localhost')->group(function () use ($publicRoutes, $authRoutes): void {
        $publicRoutes();
        $authRoutes();
    });
}

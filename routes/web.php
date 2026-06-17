<?php

use App\Http\Controllers\Public\PricingController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::view('/features', 'public.features')->name('features');
Route::get('/pricing', PricingController::class)->name('pricing');
Route::view('/use-cases/agencies', 'public.use-cases.agencies')->name('use-cases.agencies');
Route::view('/use-cases/small-business', 'public.use-cases.small-business')->name('use-cases.small-business');
Route::view('/contact', 'public.contact')->name('contact');

foreach (['auth.php', 'platform.php', 'tenant.php'] as $routeFile) {
    $path = __DIR__.'/'.$routeFile;

    if (file_exists($path)) {
        require $path;
    }
}

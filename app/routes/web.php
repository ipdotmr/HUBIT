<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

Route::post('/webhooks/stripe', [App\Http\Controllers\WebhookController::class, 'stripe'])->name('webhooks.stripe');

Route::middleware(['auth', 'verified', 'throttle:60,1'])->prefix('managit')->group(function () {
    Route::prefix('settings')->name('managit.settings.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('index');
        Route::get('/company', [App\Http\Controllers\Admin\SettingsController::class, 'company'])->name('company');
        Route::get('/themes', [App\Http\Controllers\Admin\SettingsController::class, 'themes'])->name('themes');
        Route::get('/payments/stripe', [App\Http\Controllers\Admin\SettingsController::class, 'stripe'])->name('stripe');
        Route::get('/provisioning/cpanel', [App\Http\Controllers\Admin\SettingsController::class, 'cpanel'])->name('cpanel');
        Route::post('/', [App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('update');
        Route::post('/test/{service}', [App\Http\Controllers\Admin\SettingsController::class, 'testConnection'])->name('test');
    });
});

Route::any('/admin/{any?}', function () {
    abort(404);
})->where('any', '.*');

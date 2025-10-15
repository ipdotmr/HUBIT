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
    
    Route::get('/invoices/{invoice}', [App\Http\Controllers\InvoiceController::class, 'show'])->name('invoices.show');
    Route::post('/invoices/{invoice}/pay/bank-transfer', [App\Http\Controllers\InvoiceController::class, 'payBankTransfer'])->name('invoices.pay.bank-transfer');
    Route::post('/invoices/{invoice}/pay/cash', [App\Http\Controllers\InvoiceController::class, 'payCash'])->name('invoices.pay.cash');
});

require __DIR__.'/auth.php';

Route::post('/webhooks/stripe', [App\Http\Controllers\WebhookController::class, 'stripe'])->name('webhooks.stripe');

Route::middleware(['auth', 'verified', 'throttle:60,1'])->prefix('managit')->group(function () {
    Route::get('/billing/transactions', [App\Http\Controllers\Admin\TransactionController::class, 'index'])->name('managit.transactions.index');
    Route::get('/billing/transactions/{transaction}', [App\Http\Controllers\Admin\TransactionController::class, 'show'])->name('managit.transactions.show');
    Route::post('/billing/transactions/{transaction}/review', [App\Http\Controllers\Admin\TransactionController::class, 'review'])->name('managit.transactions.review');
    
    Route::prefix('settings')->name('managit.settings.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\SettingsController::class, 'index'])->name('index');
        Route::get('/company', [App\Http\Controllers\Admin\SettingsController::class, 'company'])->name('company');
        Route::get('/themes', [App\Http\Controllers\Admin\SettingsController::class, 'themes'])->name('themes');
        Route::get('/email', [App\Http\Controllers\Admin\SettingsController::class, 'email'])->name('email');
        Route::get('/payments/stripe', [App\Http\Controllers\Admin\SettingsController::class, 'stripe'])->name('stripe');
        Route::get('/payments/paypal', [App\Http\Controllers\Admin\SettingsController::class, 'paypal'])->name('paypal');
        Route::get('/provisioning/cpanel', [App\Http\Controllers\Admin\SettingsController::class, 'cpanel'])->name('cpanel');
        Route::get('/provisioning/plesk', [App\Http\Controllers\Admin\SettingsController::class, 'plesk'])->name('plesk');
        Route::get('/domains/namecheap', [App\Http\Controllers\Admin\SettingsController::class, 'namecheap'])->name('namecheap');
        Route::get('/domains/resellerclub', [App\Http\Controllers\Admin\SettingsController::class, 'resellerclub'])->name('resellerclub');
        Route::get('/domains/namecom', [App\Http\Controllers\Admin\SettingsController::class, 'namecom'])->name('namecom');
        Route::get('/domains/coccaep', [App\Http\Controllers\Admin\SettingsController::class, 'coccaep'])->name('coccaep');
        Route::get('/payments/accounts', [App\Http\Controllers\Admin\SettingsController::class, 'paymentAccounts'])->name('payment-accounts');
        Route::get('/payments/rates', [App\Http\Controllers\Admin\SettingsController::class, 'exchangeRates'])->name('exchange-rates');
        Route::get('/localization', [App\Http\Controllers\Admin\SettingsController::class, 'localization'])->name('localization');
        Route::post('/', [App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('update');
        Route::post('/test/{service}', [App\Http\Controllers\Admin\SettingsController::class, 'testConnection'])->name('test');
    });
});

Route::any('/admin/{any?}', function () {
    abort(404);
})->where('any', '.*');

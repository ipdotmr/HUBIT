<?php

use App\Http\Controllers\Install\InstallerController as InstallWizardController;
use App\Http\Controllers\InstallerController;
use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::middleware(['ensure.not.installed', 'throttle:20,1'])->group(function () {
    Route::get('/install', [InstallWizardController::class, 'index'])->name('install.index');
    Route::post('/install/check-db', [InstallWizardController::class, 'checkDb'])->name('install.check-db');
    Route::post('/install/write-env', [InstallWizardController::class, 'writeEnv'])->name('install.write-env');
    Route::post('/install/run', [InstallWizardController::class, 'run'])->name('install.run');
});

if (! file_exists(config('installer.marker_path'))) {
    Route::middleware(['web', 'not.installed', 'throttle:30,1'])
        ->prefix('install-simple')
        ->name('install.simple.')
        ->group(function () {
            Route::get('/', [InstallerController::class, 'welcome'])->name('welcome');
            Route::get('/checks', [InstallerController::class, 'checks'])->name('checks');
            Route::get('/env', [InstallerController::class, 'envForm'])->name('env');
            Route::post('/env', [InstallerController::class, 'envSave'])->name('env.save');
            Route::get('/admin', [InstallerController::class, 'adminForm'])->name('admin');
            Route::post('/admin', [InstallerController::class, 'adminSave'])->name('admin.save');
            Route::post('/run', [InstallerController::class, 'runInstall'])->name('run');
            Route::get('/done', [InstallerController::class, 'done'])->name('done');
        });
}

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

    Route::prefix('cart')->name('cart.')->group(function () {
        Route::get('/', [App\Http\Controllers\CartController::class, 'index'])->name('index');
        Route::post('/add', [App\Http\Controllers\CartController::class, 'add'])->name('add');
        Route::post('/domain', [App\Http\Controllers\CartController::class, 'addDomain'])->name('add-domain');
        Route::post('/hosting', [App\Http\Controllers\CartController::class, 'addHosting'])->name('add-hosting');
        Route::delete('/items/{item}', [App\Http\Controllers\CartController::class, 'remove'])->name('remove');
        Route::post('/clear', [App\Http\Controllers\CartController::class, 'clear'])->name('clear');
        Route::post('/checkout', [App\Http\Controllers\CartController::class, 'checkout'])->name('checkout');
    });

    Route::prefix('domains')->name('domains.')->group(function () {
        Route::get('/search', [App\Http\Controllers\DomainSearchController::class, 'index'])->name('search');
        Route::post('/search', [App\Http\Controllers\DomainSearchController::class, 'search'])->name('search.query');
        Route::get('/pricing', [App\Http\Controllers\DomainSearchController::class, 'pricing'])->name('pricing');
    });

    Route::prefix('dashboard/domains')->name('client.domains.')->middleware('throttle:60,1')->group(function () {
        Route::get('/', [App\Http\Controllers\Client\DomainController::class, 'index'])->name('index');
        Route::get('/{domain}', [App\Http\Controllers\Client\DomainController::class, 'show'])->name('show');
        Route::put('/{domain}/nameservers', [App\Http\Controllers\Client\DomainController::class, 'updateNameservers'])->name('nameservers.update');
        Route::post('/{domain}/dns', [App\Http\Controllers\Client\DomainController::class, 'storeDnsRecord'])->name('dns.store');
        Route::put('/{domain}/dns/{record}', [App\Http\Controllers\Client\DomainController::class, 'updateDnsRecord'])->name('dns.update');
        Route::delete('/{domain}/dns/{record}', [App\Http\Controllers\Client\DomainController::class, 'destroyDnsRecord'])->name('dns.destroy');
        Route::put('/{domain}/privacy', [App\Http\Controllers\Client\DomainController::class, 'updatePrivacy'])->name('privacy.update');
        Route::put('/{domain}/lock', [App\Http\Controllers\Client\DomainController::class, 'updateLock'])->name('lock.update');
        Route::post('/{domain}/renew', [App\Http\Controllers\Client\DomainController::class, 'renew'])->name('renew');
    });

    Route::prefix('dashboard/services')->name('client.services.')->middleware('throttle:60,1')->group(function () {
        Route::get('/', [App\Http\Controllers\Client\ServiceController::class, 'index'])->name('index');
        Route::get('/{service}', [App\Http\Controllers\Client\ServiceController::class, 'show'])->name('show');
        Route::post('/{service}/reset-password', [App\Http\Controllers\Client\ServiceController::class, 'resetPassword'])->name('reset-password');
        Route::post('/{service}/sync', [App\Http\Controllers\Client\ServiceController::class, 'sync'])->name('sync');
        Route::post('/{service}/suspend', [App\Http\Controllers\Client\ServiceController::class, 'suspend'])->name('suspend');
        Route::post('/{service}/unsuspend', [App\Http\Controllers\Client\ServiceController::class, 'unsuspend'])->name('unsuspend');
        Route::post('/{service}/upgrade', [App\Http\Controllers\Client\ServiceController::class, 'upgrade'])->name('upgrade');
    });

    Route::prefix('dashboard/wallet')->name('client.wallet.')->middleware('throttle:60,1')->group(function () {
        Route::get('/', [App\Http\Controllers\Client\WalletController::class, 'index'])->name('index');
        Route::post('/add-credit', [App\Http\Controllers\Client\WalletController::class, 'addCredit'])->name('add-credit');
        Route::get('/transactions', [App\Http\Controllers\Client\WalletController::class, 'transactions'])->name('transactions');
    });

    Route::prefix('dashboard/invoices')->name('client.invoices.')->middleware('throttle:60,1')->group(function () {
        Route::get('/', [App\Http\Controllers\Client\InvoiceController::class, 'index'])->name('index');
    });

    Route::prefix('products')->name('products.')->middleware('throttle:60,1')->group(function () {
        Route::get('/', [App\Http\Controllers\Client\ProductController::class, 'index'])->name('index');
        Route::get('/{slug}', [App\Http\Controllers\Client\ProductController::class, 'show'])->name('show');
    });
});

require __DIR__.'/auth.php';

Route::post('/webhooks/stripe', [App\Http\Controllers\WebhookController::class, 'stripe'])->name('webhooks.stripe');

Route::post('/language', function (Illuminate\Http\Request $request) {
    $locale = $request->input('locale', 'en');
    if (in_array($locale, ['en', 'ar', 'fr'])) {
        session(['locale' => $locale]);
    }
    return back();
})->name('language.switch');

Route::get('/auth/microsoft', [App\Http\Controllers\Auth\SocialAuthController::class, 'redirectToMicrosoft'])->name('auth.microsoft');
Route::get('/auth/microsoft/callback', [App\Http\Controllers\Auth\SocialAuthController::class, 'handleMicrosoftCallback'])->name('auth.microsoft.callback');

Route::middleware('guest')->prefix('managit')->group(function () {
    Route::get('/', function () {
        return redirect()->route('managit.login');
    });
    Route::get('/login', [App\Http\Controllers\Auth\AdminLoginController::class, 'create'])->name('managit.login');
    Route::post('/login', [App\Http\Controllers\Auth\AdminLoginController::class, 'store']);
});

Route::middleware('auth')->prefix('managit')->group(function () {
    Route::post('/logout', [App\Http\Controllers\Auth\AdminLoginController::class, 'destroy'])->name('managit.logout');
});

Route::middleware(['auth', 'verified', 'throttle:60,1'])->prefix('managit')->group(function () {
    Route::get('/', function () {
        return redirect()->route('managit.dashboard');
    });
    Route::get('/dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('managit.dashboard');
    
    Route::resource('clients', App\Http\Controllers\Admin\ClientController::class)->names([
        'index' => 'managit.clients.index',
        'create' => 'managit.clients.create',
        'store' => 'managit.clients.store',
        'show' => 'managit.clients.show',
        'edit' => 'managit.clients.edit',
        'update' => 'managit.clients.update',
        'destroy' => 'managit.clients.destroy',
    ]);
    
    Route::prefix('orders')->name('managit.orders.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\OrderController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\OrderController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\OrderController::class, 'store'])->name('store');
        Route::get('/{order}', [App\Http\Controllers\Admin\OrderController::class, 'show'])->name('show');
        Route::post('/{order}/status', [App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('update-status');
    });
    
    Route::resource('invoices', App\Http\Controllers\Admin\InvoiceController::class)->names([
        'index' => 'managit.invoices.index',
        'create' => 'managit.invoices.create',
        'store' => 'managit.invoices.store',
        'show' => 'managit.invoices.show',
        'edit' => 'managit.invoices.edit',
        'update' => 'managit.invoices.update',
        'destroy' => 'managit.invoices.destroy',
    ]);
    
    Route::resource('domains', App\Http\Controllers\Admin\DomainController::class)->names([
        'index' => 'managit.domains.index',
        'create' => 'managit.domains.create',
        'store' => 'managit.domains.store',
        'show' => 'managit.domains.show',
        'edit' => 'managit.domains.edit',
        'update' => 'managit.domains.update',
        'destroy' => 'managit.domains.destroy',
    ]);
    
    Route::resource('products', App\Http\Controllers\Admin\ProductController::class)->names([
        'index' => 'managit.products.index',
        'create' => 'managit.products.create',
        'store' => 'managit.products.store',
        'show' => 'managit.products.show',
        'edit' => 'managit.products.edit',
        'update' => 'managit.products.update',
        'destroy' => 'managit.products.destroy',
    ]);
    
    Route::get('/billing/transactions', [App\Http\Controllers\Admin\TransactionController::class, 'index'])->name('managit.transactions.index');
    Route::get('/billing/transactions/{transaction}', [App\Http\Controllers\Admin\TransactionController::class, 'show'])->name('managit.transactions.show');
    Route::post('/billing/transactions/{transaction}/review', [App\Http\Controllers\Admin\TransactionController::class, 'review'])->name('managit.transactions.review');

    Route::prefix('products')->name('managit.products.')->group(function () {
        Route::get('/mapping', [App\Http\Controllers\Admin\ProductMappingController::class, 'index'])->name('mapping');
        Route::put('/mapping/{product}', [App\Http\Controllers\Admin\ProductMappingController::class, 'update'])->name('mapping.update');
        Route::post('/mapping/{product}/test', [App\Http\Controllers\Admin\ProductMappingController::class, 'testProvision'])->name('mapping.test');
    });

    Route::resource('email-templates', App\Http\Controllers\Admin\EmailTemplateController::class)->names([
        'index' => 'managit.email-templates.index',
        'create' => 'managit.email-templates.create',
        'store' => 'managit.email-templates.store',
        'edit' => 'managit.email-templates.edit',
        'update' => 'managit.email-templates.update',
        'destroy' => 'managit.email-templates.destroy',
    ]);

    Route::resource('currencies', App\Http\Controllers\Admin\CurrencyController::class)->names([
        'index' => 'managit.currencies.index',
        'create' => 'managit.currencies.create',
        'store' => 'managit.currencies.store',
        'edit' => 'managit.currencies.edit',
        'update' => 'managit.currencies.update',
        'destroy' => 'managit.currencies.destroy',
    ]);

    Route::resource('servers', App\Http\Controllers\Admin\ServerController::class)->names([
        'index' => 'managit.servers.index',
        'create' => 'managit.servers.create',
        'store' => 'managit.servers.store',
        'edit' => 'managit.servers.edit',
        'update' => 'managit.servers.update',
        'destroy' => 'managit.servers.destroy',
    ]);
    Route::post('servers/{id}/test', [App\Http\Controllers\Admin\ServerController::class, 'testConnection'])->name('managit.servers.test');

    Route::resource('support-departments', App\Http\Controllers\Admin\SupportDepartmentController::class)->names([
        'index' => 'managit.support-departments.index',
        'create' => 'managit.support-departments.create',
        'store' => 'managit.support-departments.store',
        'edit' => 'managit.support-departments.edit',
        'update' => 'managit.support-departments.update',
        'destroy' => 'managit.support-departments.destroy',
    ]);

    Route::resource('support-statuses', App\Http\Controllers\Admin\SupportStatusController::class)->names([
        'index' => 'managit.support-statuses.index',
        'create' => 'managit.support-statuses.create',
        'store' => 'managit.support-statuses.store',
        'edit' => 'managit.support-statuses.edit',
        'update' => 'managit.support-statuses.update',
        'destroy' => 'managit.support-statuses.destroy',
    ]);

    Route::resource('product-groups', App\Http\Controllers\Admin\ProductGroupController::class)->names([
        'index' => 'managit.product-groups.index',
        'create' => 'managit.product-groups.create',
        'store' => 'managit.product-groups.store',
        'edit' => 'managit.product-groups.edit',
        'update' => 'managit.product-groups.update',
        'destroy' => 'managit.product-groups.destroy',
    ]);

    Route::resource('quotes', App\Http\Controllers\Admin\QuoteController::class)->names([
        'index' => 'managit.quotes.index',
        'create' => 'managit.quotes.create',
        'store' => 'managit.quotes.store',
        'show' => 'managit.quotes.show',
        'edit' => 'managit.quotes.edit',
        'update' => 'managit.quotes.update',
        'destroy' => 'managit.quotes.destroy',
    ]);

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
        Route::post('/payments/accounts', [App\Http\Controllers\Admin\SettingsController::class, 'storePaymentAccount'])->name('payment-accounts.store');
        Route::put('/payments/accounts/{id}', [App\Http\Controllers\Admin\SettingsController::class, 'updatePaymentAccount'])->name('payment-accounts.update');
        Route::delete('/payments/accounts/{id}', [App\Http\Controllers\Admin\SettingsController::class, 'deletePaymentAccount'])->name('payment-accounts.delete');
        Route::get('/payments/rates', [App\Http\Controllers\Admin\SettingsController::class, 'exchangeRates'])->name('exchange-rates');
        Route::get('/localization', [App\Http\Controllers\Admin\SettingsController::class, 'localization'])->name('localization');
        Route::post('/', [App\Http\Controllers\Admin\SettingsController::class, 'update'])->name('update');
        Route::post('/test/{service}', [App\Http\Controllers\Admin\SettingsController::class, 'testConnection'])->name('test');
    });

    Route::prefix('reports')->name('managit.reports.')->group(function () {
        Route::get('/services', [App\Http\Controllers\Admin\ReportController::class, 'services'])->name('services');
        Route::get('/wallet', [App\Http\Controllers\Admin\ReportController::class, 'wallet'])->name('wallet');
        Route::get('/services/export', [App\Http\Controllers\Admin\ReportController::class, 'exportServices'])->name('services.export');
        Route::get('/wallet/export', [App\Http\Controllers\Admin\ReportController::class, 'exportWallet'])->name('wallet.export');
    });

    Route::prefix('support')->name('managit.support.')->group(function () {
        Route::get('/', [App\Http\Controllers\Admin\SupportController::class, 'index'])->name('index');
        Route::get('/create', [App\Http\Controllers\Admin\SupportController::class, 'create'])->name('create');
        Route::post('/', [App\Http\Controllers\Admin\SupportController::class, 'store'])->name('store');
        Route::get('/{ticket}', [App\Http\Controllers\Admin\SupportController::class, 'show'])->name('show');
        Route::post('/{ticket}/reply', [App\Http\Controllers\Admin\SupportController::class, 'reply'])->name('reply');
        Route::post('/{ticket}/status', [App\Http\Controllers\Admin\SupportController::class, 'updateStatus'])->name('updateStatus');
    });

    Route::prefix('utilities')->name('managit.utilities.')->group(function () {
        Route::get('/system-cleanup', [App\Http\Controllers\Admin\UtilitiesController::class, 'systemCleanup'])->name('system-cleanup');
        Route::post('/system-cleanup', [App\Http\Controllers\Admin\UtilitiesController::class, 'performCleanup'])->name('perform-cleanup');
        Route::get('/logs', [App\Http\Controllers\Admin\UtilitiesController::class, 'activityLogs'])->name('logs');
        Route::get('/database', [App\Http\Controllers\Admin\UtilitiesController::class, 'databaseStatus'])->name('database');
        Route::post('/database/optimize', [App\Http\Controllers\Admin\UtilitiesController::class, 'optimizeDatabase'])->name('database.optimize');
    });
});

Route::any('/admin/{any?}', function () {
    abort(404);
})->where('any', '.*');

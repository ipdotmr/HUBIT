<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function show(\App\Models\Invoice $invoice)
    {
        $invoice->load(['client', 'items']);

        $currencyService = app(\App\Services\Billing\CurrencyService::class);
        $paymentAccountService = app(\App\Services\Billing\PaymentAccountService::class);

        $enabledCurrencies = $currencyService->getEnabledCurrencies();
        $paymentAccounts = [];

        foreach ($enabledCurrencies as $currency) {
            $paymentAccounts[$currency['code']] = $paymentAccountService->getForClient($currency['code'], app()->getLocale());
        }

        return \Inertia\Inertia::render('Invoices/Show', [
            'invoice' => $invoice,
            'enabledCurrencies' => $enabledCurrencies,
            'paymentAccounts' => $paymentAccounts,
            'defaultCurrency' => $currencyService->getDefaultCurrency(),
        ]);
    }

    public function payBankTransfer(Request $request, \App\Models\Invoice $invoice)
    {
        $validated = $request->validate([
            'payment_account_id' => 'required|exists:payment_accounts,id',
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|size:3',
            'evidence' => 'nullable|array',
            'evidence.*.file' => 'file|mimes:pdf,jpg,jpeg,png|max:5120',
            'note' => 'nullable|string|max:500',
        ]);

        $evidence = [];
        if (isset($validated['evidence'])) {
            foreach ($validated['evidence'] as $item) {
                if (isset($item['file'])) {
                    $path = $item['file']->store('payment-evidence', 'public');
                    $evidence[] = [
                        'type' => 'file',
                        'path' => $path,
                        'name' => $item['file']->getClientOriginalName(),
                    ];
                }
            }
        }

        $service = app(\App\Services\Billing\OfflinePaymentService::class);
        $transaction = $service->requestBankTransfer(
            $invoice,
            $validated['payment_account_id'],
            $validated['amount'],
            $validated['currency'],
            $evidence
        );

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Payment submitted successfully. Reference: '.$transaction->meta['reference']);
    }

    public function payCash(Request $request, \App\Models\Invoice $invoice)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'currency' => 'required|string|size:3',
            'evidence' => 'nullable|array',
            'evidence.*.file' => 'file|mimes:pdf,jpg,jpeg,png|max:5120',
            'note' => 'nullable|string|max:500',
        ]);

        $evidence = [];
        if (isset($validated['evidence'])) {
            foreach ($validated['evidence'] as $item) {
                if (isset($item['file'])) {
                    $path = $item['file']->store('payment-evidence', 'public');
                    $evidence[] = [
                        'type' => 'file',
                        'path' => $path,
                        'name' => $item['file']->getClientOriginalName(),
                    ];
                }
            }
        }

        $service = app(\App\Services\Billing\OfflinePaymentService::class);
        $transaction = $service->requestCash(
            $invoice,
            $validated['amount'],
            $validated['currency'],
            $evidence
        );

        return redirect()->route('invoices.show', $invoice)
            ->with('success', 'Cash payment submitted successfully. Reference: '.$transaction->meta['reference']);
    }
}

<?php

namespace App\Services;

use App\Models\DomainRenewal;
use App\Models\Invoice;
use App\Models\InvoiceItem;

class InvoiceService
{
    public function createInvoiceForDomainRenewal(DomainRenewal $renewal, array $pricing): Invoice
    {
        $domain = $renewal->domain;
        $client = $domain->client;

        $invoice = Invoice::create([
            'client_id' => $client->id,
            'number' => $this->generateInvoiceNumber(),
            'status' => 'draft',
            'issue_date' => now(),
            'due_date' => now()->addDays(7),
            'subtotal' => $pricing['subtotal'],
            'tax' => $pricing['tax'],
            'total' => $pricing['total'],
            'currency' => $pricing['currency'],
        ]);

        $yearLabel = $renewal->years === 1 ? 'year' : 'years';

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'type' => 'product',
            'description' => "Domain Renewal: {$domain->domain} ({$renewal->years} {$yearLabel})",
            'qty' => 1,
            'unit_amount' => $pricing['subtotal'],
        ]);

        if ($pricing['tax'] > 0) {
            InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'type' => 'tax',
                'description' => 'Tax',
                'qty' => 1,
                'unit_amount' => $pricing['tax'],
            ]);
        }

        $invoice->update(['status' => 'open']);

        return $invoice;
    }

    protected function generateInvoiceNumber(): string
    {
        $lastInvoice = Invoice::latest('id')->first();
        $nextNumber = $lastInvoice ? ((int) substr($lastInvoice->number, 4)) + 1 : 1;

        return 'INV-'.str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}

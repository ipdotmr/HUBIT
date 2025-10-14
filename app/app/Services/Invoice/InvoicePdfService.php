<?php

namespace App\Services\Invoice;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoicePdfService
{
    public function generate(Invoice $invoice): \Illuminate\Http\Response
    {
        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $invoice->load(['client', 'invoice_items']),
            'company' => $this->getCompanyDetails(),
        ]);
        
        return $pdf->download('invoice-' . $invoice->number . '.pdf');
    }
    
    public function stream(Invoice $invoice): \Illuminate\Http\Response
    {
        $pdf = Pdf::loadView('invoices.pdf', [
            'invoice' => $invoice->load(['client', 'invoice_items']),
            'company' => $this->getCompanyDetails(),
        ]);
        
        return $pdf->stream();
    }
    
    private function getCompanyDetails(): array
    {
        return [
            'name' => config('services.company.name', 'HUBIT'),
            'address' => config('services.company.address', ''),
            'phone' => config('services.company.phone', ''),
            'email' => config('services.company.email', 'support@hubit.com'),
            'tax_id' => config('services.company.tax_id', ''),
            'logo_url' => config('services.company.logo_url', ''),
        ];
    }
}

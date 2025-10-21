<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        
        $invoices = Invoice::where('client_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return Inertia::render('Client/Invoices/Index', [
            'invoices' => $invoices,
        ]);
    }
}

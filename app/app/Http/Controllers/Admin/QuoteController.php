<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class QuoteController extends Controller
{
    public function index(Request $request)
    {
        $query = Quote::with('user')
            ->orderBy('created_at', 'desc');

        if ($request->has('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $quotes = $query->paginate(20);

        return Inertia::render('Admin/Quotes/Index', [
            'quotes' => $quotes,
            'filters' => $request->only('status'),
        ]);
    }

    public function create()
    {
        $clients = User::orderBy('name')->get(['id', 'name', 'email']);

        return Inertia::render('Admin/Quotes/Create', [
            'clients' => $clients,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'subtotal' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'valid_until' => 'nullable|date|after:today',
        ]);

        $lastQuote = Quote::latest('id')->first();
        $nextNumber = $lastQuote ? ($lastQuote->id + 1) : 1;
        $validated['quote_number'] = 'QT-' . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);

        Quote::create($validated);

        return redirect()->route('managit.quotes.index')
            ->with('success', 'Quote created successfully');
    }

    public function show($id)
    {
        $quote = Quote::with('user')->findOrFail($id);

        return Inertia::render('Admin/Quotes/Show', [
            'quote' => $quote,
        ]);
    }

    public function edit($id)
    {
        $quote = Quote::with('user')->findOrFail($id);
        $clients = User::orderBy('name')->get(['id', 'name', 'email']);

        return Inertia::render('Admin/Quotes/Edit', [
            'quote' => $quote,
            'clients' => $clients,
        ]);
    }

    public function update(Request $request, $id)
    {
        $quote = Quote::findOrFail($id);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'subject' => 'required|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|in:draft,sent,accepted,declined,expired',
            'subtotal' => 'required|numeric|min:0',
            'tax' => 'required|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'currency' => 'required|string|size:3',
            'valid_until' => 'nullable|date',
        ]);

        if ($validated['status'] === 'sent' && !$quote->sent_at) {
            $validated['sent_at'] = now();
        } elseif ($validated['status'] === 'accepted' && !$quote->accepted_at) {
            $validated['accepted_at'] = now();
        } elseif ($validated['status'] === 'declined' && !$quote->declined_at) {
            $validated['declined_at'] = now();
        }

        $quote->update($validated);

        return redirect()->route('managit.quotes.index')
            ->with('success', 'Quote updated successfully');
    }

    public function destroy($id)
    {
        $quote = Quote::findOrFail($id);
        $quote->delete();

        return redirect()->route('managit.quotes.index')
            ->with('success', 'Quote deleted successfully');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Currency;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CurrencyController extends Controller
{
    public function index()
    {
        $currencies = Currency::orderBy('is_default', 'desc')
            ->orderBy('code')
            ->get();

        return Inertia::render('Admin/Currencies/Index', [
            'currencies' => $currencies,
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Currencies/Create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|size:3|unique:currencies,code',
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10',
            'symbol_position' => 'required|in:before,after',
            'prefix' => 'nullable|string|max:10',
            'suffix' => 'nullable|string|max:10',
            'format' => 'required|string|max:50',
            'decimals' => 'required|integer|min:0|max:4',
            'exchange_rate_to_usd' => 'required|numeric|min:0',
            'is_default' => 'boolean',
            'enabled' => 'boolean',
        ]);

        if ($validated['is_default'] ?? false) {
            Currency::where('is_default', true)->update(['is_default' => false]);
        }

        Currency::create($validated);

        return redirect()->route('managit.currencies.index')
            ->with('success', 'Currency created successfully');
    }

    public function edit($code)
    {
        $currency = Currency::findOrFail($code);

        return Inertia::render('Admin/Currencies/Edit', [
            'currency' => $currency,
        ]);
    }

    public function update(Request $request, $code)
    {
        $currency = Currency::findOrFail($code);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10',
            'symbol_position' => 'required|in:before,after',
            'prefix' => 'nullable|string|max:10',
            'suffix' => 'nullable|string|max:10',
            'format' => 'required|string|max:50',
            'decimals' => 'required|integer|min:0|max:4',
            'exchange_rate_to_usd' => 'required|numeric|min:0',
            'is_default' => 'boolean',
            'enabled' => 'boolean',
        ]);

        if ($validated['is_default'] ?? false) {
            Currency::where('is_default', true)
                ->where('code', '!=', $code)
                ->update(['is_default' => false]);
        }

        $currency->update($validated);

        return redirect()->route('managit.currencies.index')
            ->with('success', 'Currency updated successfully');
    }

    public function destroy($code)
    {
        $currency = Currency::findOrFail($code);

        if ($currency->is_default) {
            return redirect()->back()
                ->with('error', 'Cannot delete the default currency');
        }

        $currency->delete();

        return redirect()->route('managit.currencies.index')
            ->with('success', 'Currency deleted successfully');
    }
}

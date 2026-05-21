<?php

namespace App\Http\Controllers\admin;

use App\Models\Currency;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CurrencyController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Currency::class);
        $query = Currency::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('symbol', 'LIKE', "%{$search}%");
        }

        $currencies = $query->latest()->paginate(10);

        return view('admin.currencies.index', compact('currencies'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $this->authorize('create', Currency::class);
        return view('admin.currencies.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Currency::class);
        $request->validate([
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10',
            'is_active' => 'sometimes|boolean',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        Currency::create($data);

        return redirect()->route('currencies.index')->with('success', 'Currency created successfully!');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Currency $currency)
    {
        $this->authorize('update', $currency);
        return view('admin.currencies.edit', compact('currency'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Currency $currency)
    {
        $this->authorize('update', $currency);
        $request->validate([
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10',
            'is_active' => 'sometimes|boolean',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        $currency->update($data);

        return redirect()->route('currencies.index')->with('success', 'Currency updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Currency $currency)
    {
        $this->authorize('delete', $currency);
        $currency->delete();
        return redirect()->route('currencies.index')->with('success', 'Currency deleted successfully!');
    }
}

<?php

namespace App\Http\Controllers\admin;

use App\Models\Table;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class TableController extends Controller
{
    /**
     * Display a listing of the tables.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Table::class);
        $query = Table::latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $tables = $query->paginate(10);

        return view('admin.tables.index', compact('tables'));
    }

    /**
     * Show the form for creating a new table.
     */
    public function create()
    {
        $this->authorize('create', Table::class);
        return view('admin.tables.create');
    }

    /**
     * Store a newly created table in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Table::class);
        $request->validate([
            'name' => 'required|string|max:255|unique:tables,name',
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:available,occupied,reserved',
        ]);

        Table::create($request->all());

        return redirect()->route('tables.index')->with('success', __('Table created successfully!'));
    }

    /**
     * Display the specified table.
     */
    public function show(Table $table)
    {
        $this->authorize('view', $table);
        return view('admin.tables.show', compact('table'));
    }

    /**
     * Show the form for editing the specified table.
     */
    public function edit(Table $table)
    {
        $this->authorize('update', $table);
        return view('admin.tables.edit', compact('table'));
    }

    /**
     * Update the specified table in storage.
     */
    public function update(Request $request, Table $table)
    {
        $this->authorize('update', $table);
        $request->validate([
            'name' => 'required|string|max:255|unique:tables,name,' . $table->id,
            'capacity' => 'required|integer|min:1',
            'status' => 'required|in:available,occupied,reserved',
        ]);

        $table->update($request->all());

        return redirect()->route('tables.index')->with('success', __('Table updated successfully!'));
    }

    /**
     * Remove the specified table from storage.
     */
    public function destroy(Table $table)
    {
        $this->authorize('delete', $table);
        $table->delete();

        return redirect()->route('tables.index')->with('success', __('Table deleted successfully!'));
    }
}

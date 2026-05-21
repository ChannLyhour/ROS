<?php

namespace App\Http\Controllers\admin;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', Category::class);
        $query = Category::latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%")
                ->orWhere('description', 'LIKE', "%{$search}%");
        }

        $categories = $query->paginate(10);

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        $this->authorize('create', Category::class);
        
        return view('admin.categories.create');
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Category::class);
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        Category::create($request->all());

        return redirect()->route('categories.index')->with('success', __('Category created successfully!'));
    }

    /**
     * Display the specified category.
     */
    public function show(Category $category)
    {
        $this->authorize('view', $category);
        return view('admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category)
    {
        $this->authorize('update', $category);
        return view('admin.categories.edit', compact('category'));
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, Category $category)
    {
        $this->authorize('update', $category);
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'status' => 'required|boolean',
        ]);

        $category->update($request->all());

        return redirect()->route('categories.index')->with('success', __('Category updated successfully!'));
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);
        $category->delete();

        return redirect()->route('categories.index')->with('success', __('Category deleted successfully!'));
    }
}

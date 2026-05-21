<?php

namespace App\Http\Controllers\admin;

use App\Models\MenuItem;
use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Helper\UploadImageHelper;

class MenuItemController extends Controller
{
    /**
     * Display a listing of the menu items.
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', MenuItem::class);
        $query = MenuItem::with('category')->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->where('category_id', $request->category);
        }

        $menuItems = $query->paginate(10);
        $categories = Category::all();

        return view('admin.menu.index', compact('menuItems', 'categories'));
    }

    /**
     * Show the form for creating a new menu item.
     */
    public function create()
    {
        $this->authorize('create', MenuItem::class);
        $categories = Category::all();
        return view('admin.menu.create', compact('categories'));
    }

    /**
     * Store a newly created menu item in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', MenuItem::class);
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:available,unavailable',
        ]);

        $image = UploadImageHelper::store($request->file('image'), 'menu-items');

        MenuItem::create($request->except('image') + ['image' => $image]);

        return redirect()->route('menu.index')->with('success', __('Menu Item created successfully!'));
    }

    /**
     * Display the specified menu item.
     */
    public function show(MenuItem $menu)
    {
        $this->authorize('view', $menu);
        return view('admin.menu.show', ['menuItem' => $menu]);
    }

    /**
     * Show the form for editing the specified menu item.
     */
    public function edit(MenuItem $menu)
    {
        $this->authorize('update', $menu);
        $categories = Category::all();
        return view('admin.menu.edit', ['menuItem' => $menu, 'categories' => $categories]);
    }

    /**
     * Update the specified menu item in storage.
     */
    public function update(Request $request, MenuItem $menu)
    {
        $this->authorize('update', $menu);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:available,unavailable',
        ]);

        $image = $menu->image;
        if ($request->hasFile('image')) {
            $image = UploadImageHelper::store($request->file('image'), 'menu-items', 'public', $menu->image);
        }

        $menu->update(array_merge($request->except('image'), ['image' => $image]));

        return redirect()->route('menu.index')->with('success', __('Menu Item updated successfully!'));
    }

    /**
     * Remove the specified menu item from storage.
     */
    public function destroy(MenuItem $menu)
    {
        $this->authorize('delete', $menu);
        if ($menu->image) {
            UploadImageHelper::delete($menu->image);
        }
        $menu->delete();

        return redirect()->route('menu.index')->with('success', __('Menu Item deleted successfully!'));
    }
}

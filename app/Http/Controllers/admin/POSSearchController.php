<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;
use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Http\Request;

class POSSearchController extends Controller
{
    /**
     * Search categories and menu items specifically for the POS interface.
     */
    public function search(Request $request)
    {
        Gate::authorize('create-orders');
        $query = $request->get('q');
        $type = $request->get('type'); // 'all', 'categories', 'items'

        if (!$query && $type !== 'categories') {
            return response()->json([]);
        }

        $results = [];

        // 1. Search Categories (or show all if type is categories)
        if (!$type || $type === 'all' || $type === 'categories') {
            $catQuery = Category::query();
            if ($query) {
                $catQuery->where('name', 'LIKE', "%{$query}%");
            }
            $categories = $catQuery->limit(20)->get();

            foreach ($categories as $cat) {
                $results[] = [
                    'title' => $cat->name,
                    'path' => __('Quick filter category in POS'),
                    'url' => 'javascript:void(0)',
                    'icon' => 'grid',
                    'category' => __('Menu Categories'),
                    'type' => 'category',
                    'id' => $cat->id
                ];
            }
        }

        // 2. Search Menu Items
        if (!$type || $type === 'all' || $type === 'items') {
            if ($query) {
                $items = MenuItem::where('name', 'LIKE', "%{$query}%")
                    ->orWhere('description', 'LIKE', "%{$query}%")
                    ->with('category')
                    ->limit(15)
                    ->get();

                foreach ($items as $item) {
                    $results[] = [
                        'title' => $item->name,
                        'path' => __('Price: ') . number_format($item->price, 2) . ' | ' . ($item->category->name ?? ''),
                        'url' => 'javascript:void(0)',
                        'icon' => 'utensils',
                        'category' => __('Dishes & Drinks'),
                        'type' => 'item',
                        'item_id' => $item->id,
                        'item_data' => $item
                    ];
                }
            }
        }

        return response()->json($results);
    }
}

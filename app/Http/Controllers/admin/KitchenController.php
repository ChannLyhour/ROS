<?php

namespace App\Http\Controllers\admin;


use Illuminate\Http\Request;

use App\Models\Order;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class KitchenController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('view-orders');
        $status = $request->get('status', 'all');

        $query = Order::with(['items.menuItem', 'diningTable', 'customer', 'payment'])
            ->whereIn('status', ['pending', 'preparing', 'ready']);

        if ($status === 'new') {
            $query->where('created_at', '>=', now()->subMinutes(15));
        } elseif ($status === 'late') {
            $query->where('created_at', '<=', now()->subMinutes(30))
                  ->where('created_at', '>=', now()->subHour());
        } elseif ($status !== 'all') {
            $query->where('status', $status);
        }

        $orders = $query->oldest()->get();

        // Get counts for badges
        $counts = [
            'all' => Order::whereIn('status', ['pending', 'preparing', 'ready'])->count(),
            'new' => Order::whereIn('status', ['pending', 'preparing', 'ready'])
                          ->where('created_at', '>=', now()->subMinutes(15))
                          ->count(),
            'pending' => Order::where('status', 'pending')->count(),
            'preparing' => Order::where('status', 'preparing')->count(),
            'ready' => Order::where('status', 'ready')->count(),
            'late' => Order::whereIn('status', ['pending', 'preparing', 'ready'])
                          ->where('created_at', '<=', now()->subMinutes(30))
                          ->where('created_at', '>=', now()->subHour())
                          ->count(),
        ];

        return view('admin.kitchen.index', compact('orders', 'counts', 'status'));
    }

    public function updateNote(Request $request, Order $order)
    {
        $order->update([
            'notes' => $request->notes
        ]);

        return redirect()->back()->with('success', 'Order note updated successfully!');
    }
}

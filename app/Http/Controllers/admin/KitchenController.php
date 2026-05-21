<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\KitchenOrder;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Gate;

class KitchenController extends Controller
{
    public function index(Request $request)
    {
        Gate::authorize('view-kitchen');
        $status = $request->get('status', 'all');

        $query = KitchenOrder::with(['order.diningTable', 'order.customer', 'order.payment']);

        if ($status === 'all') {
            $query->whereIn('cooking_status', ['pending', 'cooking']);
        } elseif ($status === 'new') {
            $query->whereIn('cooking_status', ['pending', 'cooking'])
                  ->where('created_at', '>=', now()->subMinutes(15));
        } elseif ($status === 'late') {
            $query->whereIn('cooking_status', ['pending', 'cooking'])
                  ->where('created_at', '<=', now()->subMinutes(30))
                  ->where('created_at', '>=', now()->subHour());
        } elseif ($status === 'preparing') {
            $query->where('cooking_status', 'cooking');
        } elseif ($status === 'ready') {
            $query->where('cooking_status', 'done');
        } else {
            $query->where('cooking_status', $status);
        }

        $orders = $query->oldest()->get();

        // Get counts for badges
        $counts = [
            'all' => KitchenOrder::whereIn('cooking_status', ['pending', 'cooking'])->count(),
            'new' => KitchenOrder::whereIn('cooking_status', ['pending', 'cooking'])
                          ->where('created_at', '>=', now()->subMinutes(15))
                          ->count(),
            'pending' => KitchenOrder::where('cooking_status', 'pending')->count(),
            'preparing' => KitchenOrder::where('cooking_status', 'cooking')->count(),
            'ready' => KitchenOrder::where('cooking_status', 'done')->count(),
            'late' => KitchenOrder::whereIn('cooking_status', ['pending', 'cooking'])
                          ->where('created_at', '<=', now()->subMinutes(30))
                          ->where('created_at', '>=', now()->subHour())
                          ->count(),
        ];

        return view('admin.kitchen.index', compact('orders', 'counts', 'status'));
    }

    public function updateNote(Request $request, KitchenOrder $kitchenOrder)
    {
        Gate::authorize('view-kitchen');
        $kitchenOrder->update([
            'notes' => $request->notes
        ]);

        return redirect()->back()->with('success', 'Kitchen item note updated successfully!');
    }

    public function updateStatus(Request $request, KitchenOrder $kitchenOrder)
    {
        Gate::authorize('view-kitchen');
        $request->validate([
            'status' => 'required|in:pending,cooking,done'
        ]);

        $kitchenOrder->update([
            'cooking_status' => $request->status
        ]);

        // Automatically update the main Order status
        $order = $kitchenOrder->order;
        if ($order) {
            $allStatuses = $order->kitchenOrders()->pluck('cooking_status')->unique()->toArray();
            
            if (count($allStatuses) === 1 && $allStatuses[0] === 'done') {
                $order->update(['status' => 'ready']);
            } elseif (count($allStatuses) === 1 && $allStatuses[0] === 'pending') {
                $order->update(['status' => 'pending']);
            } else {
                $order->update(['status' => 'preparing']);
            }
        }

        return redirect()->back()->with('success', 'Cooking status updated successfully!');
    }
}

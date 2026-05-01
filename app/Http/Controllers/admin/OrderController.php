<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Category;
use App\Models\MenuItem;
use App\Models\Table;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Services\OrderService;
use App\Http\Requests\StoreOrderRequest;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->orderService = $orderService;
    }

    /**
     * Display a listing of orders.
     */
    public function index(Request $request)
    {
        $query = Order::with(['customer', 'diningTable', 'user'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('order_no', 'LIKE', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('order_type', $request->type);
        }

        $orders = $query->paginate(10);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new order.
     */
    public function create(Request $request)
    {
        $menuItems = MenuItem::where('status', 'available')->get();
        $tables = Table::where('status', 'available')->get();
        $customers = Customer::all();
        $categories = Category::all();

        $existingOrder = null;
        if ($request->has('order_id')) {
            $existingOrder = Order::with('items.menuItem')->find($request->order_id);
            if ($existingOrder && $existingOrder->status === 'completed') {
                return redirect()->route('orders.show', $existingOrder->id)->with('error', 'This order is already completed.');
            }
        }

        $initialCart = $this->mapOrderToCart($existingOrder);

        return view('admin.orders.checkout', compact('menuItems', 'tables', 'customers', 'categories', 'existingOrder', 'initialCart'));
    }

    /**
     * Store a newly created order.
     */
    public function store(StoreOrderRequest $request)
    {
        try {
            $order = $this->orderService->processOrder($request->validated());
            
            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order processed successfully!',
                    'order_id' => $order->id,
                    'order' => $order
                ]);
            }

            return redirect()->route('orders.index')->with('success', 'Order processed successfully!');
        } catch (\Exception $e) {
            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
            }
            return redirect()->back()->with('error', 'Order processing failed: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified order.
     */
    public function edit(Order $order)
    {
        $categories = Category::all();
        $menuItems = MenuItem::with('category')->get();
        $tables = Table::all();
        $customers = Customer::all();
        
        $existingOrder = $order->load('items.menuItem');
        $initialCart = $this->mapOrderToCart($existingOrder);
        
        return view('admin.orders.checkout', compact('menuItems', 'tables', 'customers', 'categories', 'existingOrder', 'initialCart'));
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        $order->load(['items.menuItem', 'customer', 'diningTable', 'user', 'payment']);
        $appSettings = \App\Models\Setting::pluck('value', 'key')->toArray();
        return view('admin.orders.show', compact('order', 'appSettings'));
    }

    /**
     * Update the order status.
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,preparing,ready,completed,cancelled',
        ]);

        $order->update(['status' => $request->status]);

        // Release table if completed or cancelled
        if (in_array($request->status, ['completed', 'cancelled']) && $order->table_id) {
            $order->diningTable->update(['status' => 'available']);
        }

        return redirect()->back()->with('success', 'Order status updated to ' . ucfirst($request->status));
    }

    /**
     * Remove the specified order.
     */
    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Order deleted successfully!');
    }

    /**
     * Helper to map order items to the JS cart format.
     */
    private function mapOrderToCart($order)
    {
        if (!$order) return [];

        return $order->items->map(function ($item) {
            return [
                'id' => (int) $item->menu_item_id,
                'name' => optional($item->menuItem)->name ?? 'Unknown Item',
                'price' => (float) $item->price,
                'display_image' => optional($item->menuItem)->display_image ?? asset('images/placeholder.jpg'),
                'qty' => (int) ($item->quantity ?? 1)
            ];
        })->values()->toArray();
    }
}

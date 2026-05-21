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
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;

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
        $this->authorize('viewAny', Order::class);
        $query = Order::with(['customer', 'diningTable', 'user', 'createdBy'])->latest();

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

        $orders = $query->paginate(15);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Show the form for creating a new order.
     */
    public function create(Request $request)
    {
        $query = MenuItem::where('status', 'available');
        
        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $menuItems = $query->paginate(16);
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
        $appSettings = \App\Models\Setting::pluck('value', 'key')->toArray();

        if ($request->ajax()) {
            return view('admin.orders.partials.menu_grid', compact('menuItems', 'appSettings'))->render();
        }

        return view('admin.orders.checkout', compact('menuItems', 'tables', 'customers', 'categories', 'existingOrder', 'initialCart', 'appSettings'));
    }

    /**
     * Show the dedicated POS checkout/payment page.
     */
    public function checkout(Request $request)
    {
        $existingOrder = null;
        if ($request->has('order_id')) {
            $existingOrder = Order::with('items.menuItem')->find($request->order_id);
            if ($existingOrder && $existingOrder->status === 'completed') {
                return redirect()->route('orders.show', $existingOrder->id)->with('error', 'This order is already completed.');
            }
        }

        $tables = Table::all();
        $customers = Customer::all();

        return view('admin.orders.checkout_page', compact('existingOrder', 'tables', 'customers'));
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
            Log::error('Order Store Failed: ' . $e->getMessage(), [
                'exception' => $e,
                'payload' => $request->all()
            ]);

            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Order processing failed: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->withInput()->with('error', 'Order processing failed: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing the specified order.
     */
    public function edit(Request $request, Order $order)
    {
        $query = MenuItem::with('category')->where('status', 'available');

        if ($request->filled('category') && $request->category !== 'all') {
            $query->where('category_id', $request->category);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $menuItems = $query->paginate(16);
        $categories = Category::all();
        $tables = Table::all();
        $customers = Customer::all();

        $existingOrder = $order->load('items.menuItem');
        $initialCart = $this->mapOrderToCart($existingOrder);
        $appSettings = \App\Models\Setting::pluck('value', 'key')->toArray();

        if ($request->ajax()) {
            return view('admin.orders.partials.menu_grid', compact('menuItems', 'appSettings'))->render();
        }

        return view('admin.orders.edit', compact('menuItems', 'tables', 'customers', 'categories', 'existingOrder', 'initialCart', 'appSettings'));
    }

    /**
     * Update the specified order in storage.
     */
    public function update(StoreOrderRequest $request, Order $order)
    {
        try {
            $data = $request->validated();
            $data['order_id'] = $order->id; // Enforce route parameter order_id

            $updatedOrder = $this->orderService->processOrder($data);

            if ($request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order updated successfully!',
                    'order_id' => $updatedOrder->id,
                    'order' => $updatedOrder
                ]);
            }

            return redirect()->route('orders.index')->with('success', 'Order updated successfully!');
        } catch (\Exception $e) {
            Log::error('Order Update Failed for ID ' . $order->id . ': ' . $e->getMessage(), [
                'exception' => $e,
                'payload' => $request->all()
            ]);

            if ($request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Order update failed: ' . $e->getMessage()], 500);
            }
            return redirect()->back()->withInput()->with('error', 'Order update failed: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified order.
     */
    public function show(Order $order)
    {
        $this->authorize('view', $order);
        $order->load(['items.menuItem', 'customer', 'diningTable', 'user', 'payment']);
        $appSettings = \App\Models\Setting::pluck('value', 'key')->toArray();
        return view('admin.orders.show', compact('order', 'appSettings'));
    }

    /**
     * Update the order status.
     */
    public function updateStatus(Request $request, Order $order)
    {
        if (!Gate::check('edit-orders') && !Gate::check('view-kitchen')) {
            abort(403, 'THIS ACTION IS UNAUTHORIZED.');
        }

        $request->validate([
            'status' => 'required|in:pending,preparing,ready,completed,cancelled',
        ]);

        $this->orderService->updateStatus($order, $request->status);

        return redirect()->back()->with('success', 'Order status updated to ' . ucfirst($request->status));
    }

    /**
     * Remove the specified order.
     */
    public function destroy(Order $order)
    {
        $this->authorize('delete', $order);
        $this->orderService->deleteOrder($order);
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

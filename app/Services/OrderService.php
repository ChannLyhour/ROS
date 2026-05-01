<?php

namespace App\Services;

use App\Models\Order;
use App\Models\MenuItem;
use App\Models\OrderItem;
use App\Models\Table;
use Illuminate\Support\Facades\DB;
use App\Helper\SystemHelper;

class OrderService
{
    /**
     * Process the entire order flow.
     */
    public function processOrder(array $data)
    {
        return DB::transaction(function () use ($data) {
            $order = $this->createOrUpdateOrder($data);
            $this->processItems($order, $data['items']);
            $this->finalizeOrder($order, $data);

            return $order->load(['items.menuItem', 'payment', 'diningTable']);
        });
    }

    /**
     * Create a new order or update an existing one.
     */
    protected function createOrUpdateOrder(array $data)
    {
        $attributes = [
            'order_type' => $data['order_type'],
            'customer_id' => $data['customer_id'] ?? null,
            'table_id' => $data['table_id'] ?? null,
            'notes' => $data['notes'] ?? null,
        ];

        if (!empty($data['order_id'])) {
            $order = Order::findOrFail($data['order_id']);
            $order->items()->delete(); // Clear old items
            $order->update($attributes);
            return $order;
        }

        return Order::create(array_merge($attributes, [
            'order_no' => SystemHelper::generateOrderNo(),
            'user_id' => auth()->id() ?? 1,
            'status' => 'pending',
        ]));
    }

    /**
     * Map items, batch insert, and calculate totals.
     */
    protected function processItems(Order $order, array $items)
    {
        $menuItemIds = collect($items)->pluck('menu_item_id')->unique();
        $menuItems = MenuItem::whereIn('id', $menuItemIds)->get()->keyBy('id');

        $total = 0;
        $itemsToInsert = [];

        foreach ($items as $itemData) {
            $menuItem = $menuItems->get($itemData['menu_item_id']);
            if (!$menuItem) continue;

            $quantity = $itemData['quantity'];
            $price = $menuItem->price;
            $subtotal = $price * $quantity;
            $total += $subtotal;

            $itemsToInsert[] = [
                'order_id' => $order->id,
                'menu_item_id' => $menuItem->id,
                'quantity' => $quantity,
                'price' => $price,
                'subtotal' => $subtotal,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        OrderItem::insert($itemsToInsert);

        $taxRate = (float) SystemHelper::getSetting('tax_percentage', 10) / 100;
        $tax = $total * $taxRate;

        $order->update([
            'subtotal' => $total,
            'tax' => $tax,
            'total_amount' => $total + $tax,
        ]);
    }

    /**
     * Handle payment and table status updates.
     */
    protected function finalizeOrder(Order $order, array $data)
    {
        if (!empty($data['payment_method'])) {
            $paid = $data['paid_amount'] ?? $order->total_amount;
            $change = max(0, $paid - $order->total_amount);

            $order->payment()->updateOrCreate(
                ['order_id' => $order->id],
                [
                    'payment_method' => $data['payment_method'],
                    'total_amount' => $order->total_amount,
                    'paid_amount' => $paid,
                    'change_amount' => $change,
                    'status' => 'paid',
                    'paid_at' => now(),
                ]
            );

            $order->update(['status' => 'completed']);

            if ($order->order_type === 'dine_in' && $order->table_id) {
                Table::where('id', $order->table_id)->update(['status' => 'available']);
            }
        } elseif ($order->order_type === 'dine_in' && $order->table_id) {
            Table::where('id', $order->table_id)->update(['status' => 'occupied']);
        }
    }
}

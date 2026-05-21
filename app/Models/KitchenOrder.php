<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KitchenOrder extends Model
{
    protected $fillable = ['order_id', 'item_name', 'quantity', 'cooking_status', 'notes'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

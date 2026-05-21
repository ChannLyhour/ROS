<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'order_id', 'payment_method', 'total_amount', 'paid_amount', 'change_amount', 
        'status', 'payer_name', 'payer_account', 'khqr_md5', 'khqr_string',
        'khqr_transaction_id', 'khqr_expires_at', 'paid_at'
    ];

    protected $casts = [
        'khqr_expires_at' => 'datetime',
        'paid_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

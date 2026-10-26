<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payments extends Model
{
    protected $fillable = [
        'user_id',
        'order_id',
        'payment_id',
        'signature',
        'amount',
        'currency',
        'status',
        'booking_data',
    ];

    protected $casts = [
        'booking_data' => 'array',
    ];
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'lead_id',
        'client_id',
        'product_id',
        'payment_id',
        'fee',
        'amount',
        'total',
        'currency',
        'payment_method',
        'payment_status',
        'payment_gateway',
        'payment_gateway_id',
        'last_activity',
    ];
}

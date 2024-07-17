<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentRequest extends Model
{
    use HasFactory;

    protected $table = 'payments_requests';

    protected $fillable = [
        'user_id',
        'order_id',
        'request',
        'response',
        'ip_address',
        'user_agent',
        'last_activity',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}

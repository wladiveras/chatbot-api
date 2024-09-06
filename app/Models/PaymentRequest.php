<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class PaymentRequest extends Model
{
    use HasFactory, HasUuids;

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

    public function scopeUser(Builder $query): Builder
    {
        return $query->where('user_id', auth()->id());
    }
}

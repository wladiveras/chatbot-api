<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'orders';

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

    public function scopeAuth(Builder $query): Builder
    {
        return $query->where('user_id', auth()->id());
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function payment()
    {
        return $this->belongsTo(PaymentRequest::class);
    }

    // no futuro passar os pedidos separados
    // public function orderItems()
    // {
    //     return $this->hasMany(OrderItem::class);
    // }

}

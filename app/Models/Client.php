<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Client extends Model
{
    use HasFactory;

    protected $table = 'clients';

    protected $fillable = [
        'lead_id',
        'product_id',
        'user_id',
        'name',
        'phone',
        'email',
        'address',
        'complement',
        'city',
        'state',
        'country',
        'zipcode',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeUser(Builder $query): Builder
    {
        return $query->where('user_id', auth()->id());
    }
}

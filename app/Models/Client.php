<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    use HasFactory;

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
}

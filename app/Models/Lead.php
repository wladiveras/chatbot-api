<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $table = 'leads';

    protected $fillable = [
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

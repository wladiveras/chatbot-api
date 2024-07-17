<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConnectionSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'connection_id',
        'name',
        'description',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flow extends Model
{
    use HasFactory;

    protected $table = 'flows';

    protected $fillable = [
        'user_id',
        'name',
        'payload',
        'is_active',
        'is_public',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

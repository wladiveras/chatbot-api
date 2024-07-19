<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Connection extends Model
{
    use HasFactory;

    protected $table = 'connections';

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'connection_key',
        'token',
        'type',
        'is_active',
        'country',
        'payload',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function connectionSettings()
    {
        return $this->hasOne(ConnectionSetting::class);
    }
}

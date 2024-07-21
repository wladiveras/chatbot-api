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
        'flow_id',
        'name',
        'description',
        'connection_key',
        'token',
        'type',
        'is_active',
        'country',
        'payload',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function flow()
    {
        return $this->hasOne(Flow::class);
    }

    public function connectionSettings()
    {
        return $this->hasOne(ConnectionSetting::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ConnectionSetting extends Model
{
    use HasFactory;

    protected $table = 'connections_settings';

    protected $fillable = [
        'user_id',
        'connection_id',
        'name',
        'description',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function connection()
    {
        return $this->belongsTo(Connection::class);
    }

    public function scopeUser(Builder $query): Builder
    {
        return $query->where('user_id', auth()->id());
    }
}

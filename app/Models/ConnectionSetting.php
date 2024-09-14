<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function scopeAuth(Builder $query): Builder
    {
        return $query->where('user_id', auth()->id());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function connection(): BelongsTo
    {
        return $this->belongsTo(Connection::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConnectionProfile extends Model
{
    use HasFactory;

    protected $table = 'connection_profiles';

    protected $fillable = [
        'user_id',
        'connection_id',
        'connection_key',
        'name',
        'connection_key',
        'number_exists',
        'picture',
        'is_business',
        'email',
        'description',
        'website',
    ];

    public function scopeAuth(Builder $query): Builder
    {
        return $query->where('user_id', auth()->id());
    }

    public function connection(): BelongsTo
    {
        return $this->belongsTo(Connection::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

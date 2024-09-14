<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function scopeAuth(Builder $query): Builder
    {
        return $query->where('user_id', auth()->id());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function flow(): HasOne
    {
        return $this->hasOne(Flow::class);
    }

    public function connectionSettings(): HasOne
    {
        return $this->hasOne(ConnectionSetting::class);
    }

    public function connectionProfile(): HasOne
    {
        return $this->hasOne(ConnectionProfile::class);
    }
}

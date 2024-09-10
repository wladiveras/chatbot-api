<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Flow extends Model
{
    use HasFactory;

    protected $table = 'flows';

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'node',
        'edge',
        'commands',
        'is_active',
        'is_public',
        'recovery_flow_id',
        'finished_flow_id',
        'recovery_days',
        'finished_days',
        'type',
    ];

    public function scopeAuth(Builder $query): Builder
    {
        return $query->where('user_id', auth()->id());
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

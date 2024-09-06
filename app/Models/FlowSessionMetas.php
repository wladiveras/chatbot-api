<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class FlowSessionMetas extends Model
{
    use HasFactory;

    protected $table = 'flow_session_metas';

    protected $fillable = [
        'user_id',
        'flow_session_id',
        'key',
        'value',
        'type',
    ];

    public function flowSession()
    {
        return $this->belongsTo(FlowSession::class);
    }

    public function scopeUser(Builder $query): Builder
    {
        return $query->where('user_id', auth()->id());
    }
}

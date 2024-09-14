<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlowSession extends Model
{
    use HasFactory;

    protected $table = 'flow_sessions';

    protected $fillable = [
        'flow_id',
        'connection_id',
        'session_key',
        'country',
        'step',
        'is_running',
        'last_active',
        'session_start',
        'session_end',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function flow(): BelongsTo
    {
        return $this->belongsTo(Flow::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    use HasFactory;

    protected $table = 'messages';

    protected $fillable = [
        'flow_id',
        'flow_session_id',
        'connection_id',
        'name',
        'content',
        'type',
        'origin',
        'payload',
    ];

    public function connection(): BelongsTo
    {
        return $this->belongsTo(Connection::class);
    }

    public function flow(): BelongsTo
    {
        return $this->belongsTo(Flow::class);
    }

    public function flowSession(): BelongsTo
    {
        return $this->belongsTo(FlowSession::class);
    }
}

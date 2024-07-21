<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $table = 'messages';

    protected $fillable = [
        'flow_id',
        'flow_session_id',
        'content',
        'type',
        'origin',
        'payload',
    ];

    public function flow()
    {
        return $this->belongsTo(Flow::class);
    }

    public function flowSession()
    {
        return $this->belongsTo(FlowSession::class);
    }
}

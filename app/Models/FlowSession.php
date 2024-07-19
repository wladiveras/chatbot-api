<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlowSession extends Model
{
    use HasFactory;

    protected $table = 'flows_sessions';

    protected $fillable = [
        'flow_id',
        'connection_id',
        'session_key',
        'session_start',
        'session_end',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function flow()
    {
        return $this->belongsTo(Flow::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}

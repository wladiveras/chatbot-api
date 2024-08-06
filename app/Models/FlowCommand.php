<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlowCommand extends Model
{
    use HasFactory;

    protected $table = 'flow_commands';

    protected $fillable = [
        'flow_id',
        'node_id',
        'step',
        'type',
        'value',
        'action',
        'name',
        'delay',
        'caption',
    ];

    public function flow()
    {
        return $this->belongsTo(Flow::class);
    }
}

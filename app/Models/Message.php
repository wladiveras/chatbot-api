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
        'content',
    ];

    public function flow()
    {
        return $this->belongsTo(Flow::class);
    }
}

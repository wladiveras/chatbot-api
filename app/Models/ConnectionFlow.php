<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConnectionFlow extends Model
{
    use HasFactory;
    protected $table = 'connections_has_flows';
    protected $fillable = [
        'user_id',
        'flow_id',
        'connection_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function flow()
    {
        return $this->belongsTo(Flow::class);
    }

    public function connection()
    {
        return $this->belongsTo(Connection::class);
    }
}

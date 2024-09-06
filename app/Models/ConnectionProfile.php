<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ConnectionProfile extends Model
{
    use HasFactory;

    protected $table = 'connection_profiles';

    protected $fillable = [
        'user_id',
        'connection_id',
        'connection_key',
        'name',
        'connection_key',
        'number_exists',
        'picture',
        'is_business',
        'email',
        'description',
        'website',
    ];

    public function connection()
    {
        return $this->belongsTo(Connection::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUser(Builder $query): Builder
    {
        return $query->where('user_id', auth()->id());
    }
}

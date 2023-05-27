<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JwtSession extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'jwt_sessions';


    protected $fillable = [
        "user_id",
        "session_token",
        "expires_at",
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'expires_at' => 'datetime',
    ];
}

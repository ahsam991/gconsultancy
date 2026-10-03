<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class LoginHistory extends Model
{
    protected $fillable = ['user_id', 'email', 'ip', 'user_agent', 'device', 'browser', 'successful', 'session_id'];

    protected $casts = ['successful' => 'boolean'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


}

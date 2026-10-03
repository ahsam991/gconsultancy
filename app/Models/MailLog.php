<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class MailLog extends Model
{
    protected $fillable = ['to_email', 'subject', 'template_slug', 'status', 'error', 'sent_at'];

    protected $casts = ['sent_at' => 'datetime'];



}

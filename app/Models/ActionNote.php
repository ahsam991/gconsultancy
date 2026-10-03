<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActionNote extends Model
{
    protected $fillable = ['candidate_id', 'application_id', 'user_id', 'title', 'body', 'is_pinned'];

    protected $casts = ['is_pinned' => 'boolean'];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }
}

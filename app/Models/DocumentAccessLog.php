<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class DocumentAccessLog extends Model
{
    protected $fillable = ['candidate_document_id', 'user_id', 'action', 'ip', 'purpose'];

    public function candidateDocument(): BelongsTo
    {
        return $this->belongsTo(CandidateDocument::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


}

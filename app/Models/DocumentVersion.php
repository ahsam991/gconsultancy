<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentVersion extends Model
{
    protected $fillable = ['candidate_document_id', 'version', 'stored_path', 'original_filename', 'mime', 'size_kb', 'uploaded_by', 'note'];

    public function document(): BelongsTo
    {
        return $this->belongsTo(CandidateDocument::class, 'candidate_document_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}

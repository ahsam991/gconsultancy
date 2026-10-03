<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CandidateDocument extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['candidate_id', 'application_id', 'document_type_id', 'original_filename', 'stored_path', 'mime', 'size_kb', 'verification_status', 'version', 'uploaded_by', 'verified_by', 'verified_at', 'rejection_reason', 'notes', 'expiry_date'];

    protected $casts = ['verified_at' => 'datetime'];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(DocumentVersion::class)->orderBy('version');
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('verification_status', $status);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where('original_filename', 'like', "%{$term}%");
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseRequirement extends Model
{
    protected $fillable = ['course_id', 'document_type_id', 'required', 'note'];

    protected $casts = ['required' => 'boolean'];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function documentType(): BelongsTo
    {
        return $this->belongsTo(DocumentType::class);
    }
}

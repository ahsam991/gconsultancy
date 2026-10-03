<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class CustomFieldValue extends Model
{
    protected $fillable = ['custom_field_id', 'related_type', 'related_id', 'value'];

    public function customField(): BelongsTo
    {
        return $this->belongsTo(CustomField::class);
    }

    public function scopeFor($query, $type, $id)
    {
        return $query->where('related_type', $type)->where('related_id', $id);
    }


}

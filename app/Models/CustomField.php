<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class CustomField extends Model
{
    protected $fillable = ['module', 'name', 'label', 'field_type', 'options', 'is_required', 'sort_order', 'active'];

    protected $casts = ['options' => 'array', 'is_required' => 'boolean', 'sort_order' => 'integer', 'active' => 'boolean'];

    public function values(): HasMany
    {
        return $this->hasMany(CustomFieldValue::class);
    }


}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnglishTest extends Model
{
    protected $fillable = ['candidate_id', 'test_type', 'overall', 'listening', 'reading', 'writing', 'speaking', 'test_date', 'expiry_date', 'certificate_path'];

    protected $casts = ['overall' => 'decimal:1', 'listening' => 'decimal:1', 'reading' => 'decimal:1', 'writing' => 'decimal:1', 'speaking' => 'decimal:1', 'test_date' => 'date', 'expiry_date' => 'date'];

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }
}

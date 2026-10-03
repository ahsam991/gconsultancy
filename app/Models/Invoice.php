<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use \Illuminate\Database\Eloquent\SoftDeletes;

    protected $fillable = ['invoice_number', 'university_id', 'commission_id', 'candidate_id', 'application_id', 'issue_date', 'due_date', 'subtotal', 'tax_percent', 'tax_amount', 'total', 'status', 'notes', 'pdf_path'];

    protected $casts = ['issue_date' => 'date', 'due_date' => 'date', 'subtotal' => 'decimal:2', 'tax_percent' => 'decimal:2', 'tax_amount' => 'decimal:2', 'total' => 'decimal:2'];

    public function university(): BelongsTo
    {
        return $this->belongsTo(University::class);
    }

    public function commission(): BelongsTo
    {
        return $this->belongsTo(Commission::class);
    }

    public function candidate(): BelongsTo
    {
        return $this->belongsTo(Candidate::class);
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeSearch($query, $term)
    {
        return $query->where('invoice_number', 'like', "%{$term}%");
    }
}

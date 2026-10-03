<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = ['candidate_name', 'country', 'university', 'rating', 'content', 'photo', 'is_featured', 'is_published'];

    protected $casts = ['is_featured' => 'boolean', 'is_published' => 'boolean'];

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }
}

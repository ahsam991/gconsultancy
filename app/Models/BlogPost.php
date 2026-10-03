<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class BlogPost extends Model
{
    protected $fillable = ['title', 'slug', 'blog_category_id', 'excerpt', 'content', 'featured_image', 'author_id', 'status', 'published_at', 'meta_title', 'meta_description'];

    protected $casts = ['published_at' => 'datetime'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }


}

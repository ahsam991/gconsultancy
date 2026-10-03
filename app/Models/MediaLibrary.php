<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class MediaLibrary extends Model
{
    protected $table = 'media_library';

    protected $fillable = ['name', 'path', 'mime', 'size_kb', 'alt', 'uploaded_by'];

    protected $casts = ['size_kb' => 'integer'];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }


}

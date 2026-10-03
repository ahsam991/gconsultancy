<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class Banner extends Model
{
    protected $fillable = ['title', 'image', 'link', 'location', 'active', 'sort_order'];

    protected $casts = ['active' => 'boolean', 'sort_order' => 'integer'];



}

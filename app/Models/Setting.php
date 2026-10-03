<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value', 'group'];

    public static function get(string $key, $default = null)
    {
        try {
            return cache()->rememberForever('setting.'.$key, fn () => static::where('key', $key)->value('value') ?? $default);
        } catch (\Throwable $e) {
            return $default;
        }
    }

    public static function flushCache(): void
    {
        foreach (static::pluck('key') as $key) {
            cache()->forget('setting.'.$key);
        }
    }

    public function scopeGroup($query, $group)
    {
        return $query->where('group', $group);
    }
}

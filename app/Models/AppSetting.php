<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, mixed $default = null): mixed
    {
        $row = static::query()->where('key', $key)->first();
        if (! $row) {
            return $default;
        }
        // Try JSON decode, then fallback to string
        $val = $row->value;
        if (is_string($val)) {
            $decoded = json_decode($val, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $decoded;
            }
        }

        return $val;
    }

    public static function put(string $key, mixed $value): void
    {
        $payload = is_array($value) || is_object($value) ? json_encode($value) : (string) $value;
        static::query()->updateOrCreate(['key' => $key], ['value' => $payload]);
    }
}

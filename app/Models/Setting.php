<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $table = 'settings';

    protected $fillable = [
        'key',
        'value',
        'deskripsi',
    ];

    /**
     * Cache in-memory per-request untuk menghindari N+1 query ke tabel settings
     */
    protected static ?array $runtimeCache = null;

    public static function get(string $key, $default = null)
    {
        if (static::$runtimeCache === null) {
            try {
                static::$runtimeCache = static::pluck('value', 'key')->all();
            } catch (\Throwable $e) {
                return $default;
            }
        }

        return static::$runtimeCache[$key] ?? $default;
    }

    public static function set(string $key, $value, ?string $deskripsi = null): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'deskripsi' => $deskripsi]
        );

        if (static::$runtimeCache !== null) {
            static::$runtimeCache[$key] = $value;
        }
    }

    public static function clearRuntimeCache(): void
    {
        static::$runtimeCache = null;
    }
}

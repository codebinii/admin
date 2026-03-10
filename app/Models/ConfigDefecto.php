<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Read-only model for the shared `03config_defecto` table.
 * Never write to this table — it belongs to the host system.
 */
final class ConfigDefecto extends Model
{
    protected $table = '03config_defecto';

    // Prevent accidental writes
    protected $guarded = ['*'];

    public static function main(): self
    {
        return static::firstOrFail();
    }
}

<?php

namespace App\Models;

use App\Services\SettingService;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['group', 'key', 'value', 'type'];

    protected static function booted(): void
    {
        // Any admin edit clears the cached settings so the whole site updates at once.
        static::saved(fn () => app(SettingService::class)->flush());
        static::deleted(fn () => app(SettingService::class)->flush());
    }
}

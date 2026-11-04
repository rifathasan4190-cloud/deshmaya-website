<?php

use App\Services\SettingService;
use Illuminate\Support\Facades\Storage;

if (! function_exists('setting')) {
    /** setting('contact.phone_primary', 'fallback') */
    function setting(string $path, mixed $default = null): mixed
    {
        $value = app(SettingService::class)->get($path);

        return ($value === null || $value === '') ? $default : $value;
    }
}

if (! function_exists('setting_group')) {
    function setting_group(string $group): array
    {
        return app(SettingService::class)->group($group);
    }
}

if (! function_exists('setting_url')) {
    /** Public URL for an uploaded file stored in settings (logo, favicon, loader image). */
    function setting_url(string $path, ?string $default = null): ?string
    {
        $value = setting($path);

        if (! $value) {
            return $default;
        }

        if (str_starts_with($value, 'http') || str_starts_with($value, '/')) {
            return $value;
        }

        return Storage::disk('public')->url($value);
    }
}

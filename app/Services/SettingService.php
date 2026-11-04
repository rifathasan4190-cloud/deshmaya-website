<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    public const CACHE_KEY = 'site.settings';

    /** @return array<string, array<string, mixed>> */
    public function all(): array
    {
        try {
            return Cache::rememberForever(self::CACHE_KEY, function () {
                return Setting::query()->get()
                    ->groupBy('group')
                    ->map(fn ($rows) => $rows->pluck('value', 'key')->all())
                    ->all();
            });
        } catch (\Throwable $e) {
            return []; // table missing (before migrate) or DB down: fall back to defaults
        }
    }

    public function group(string $group): array
    {
        return $this->all()[$group] ?? [];
    }

    public function get(string $path, mixed $default = null): mixed
    {
        [$group, $key] = array_pad(explode('.', $path, 2), 2, null);

        return $this->all()[$group][$key] ?? $default;
    }

    public function set(string $path, mixed $value, string $type = 'text'): void
    {
        [$group, $key] = explode('.', $path, 2);

        Setting::updateOrCreate(['group' => $group, 'key' => $key], ['value' => $value, 'type' => $type]);
    }

    public function flush(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}

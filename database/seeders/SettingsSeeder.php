<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        foreach (config('site-settings.groups', []) as $group => $definition) {
            foreach ($definition['values'] as $key => $value) {
                // firstOrCreate: re-seeding never overwrites values edited in admin
                Setting::firstOrCreate(
                    ['group' => $group, 'key' => $key],
                    ['value' => $value, 'type' => $definition['type']],
                );
            }
        }
    }
}

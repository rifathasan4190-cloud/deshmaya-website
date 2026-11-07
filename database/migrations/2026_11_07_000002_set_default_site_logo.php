<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('settings')->updateOrInsert(
            ['group' => 'general', 'key' => 'logo'],
            [
                'value' => '/images/logo.png',
                'type' => 'text',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        );

        Cache::forget('site.settings');
    }

    public function down(): void
    {
        DB::table('settings')
            ->where('group', 'general')
            ->where('key', 'logo')
            ->update(['value' => '', 'updated_at' => now()]);

        Cache::forget('site.settings');
    }
};

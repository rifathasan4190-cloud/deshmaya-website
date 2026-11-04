<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('admin.seed_email');
        $password = config('admin.seed_password');

        if (blank($email) || blank($password)) {
            return;
        }

        $admin = User::query()->firstOrNew(['email' => $email]);
        $admin->fill([
            'name' => config('admin.seed_name'),
            'password' => $password,
            'email_verified_at' => now(),
        ]);
        $admin->forceFill(['is_admin' => true])->save();
    }
}

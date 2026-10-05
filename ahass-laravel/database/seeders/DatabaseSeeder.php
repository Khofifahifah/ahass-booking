<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::query()->create([
            'name' => 'Admin Bengkel',
            'username' => 'admin',
            'email' => 'admin@ahass.test',
            'password' => 'admin123',
        ]);

        $this->call(PackageSeeder::class);
    }
}

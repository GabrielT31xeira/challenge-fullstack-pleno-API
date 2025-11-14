<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin
        User::factory()->admin()->create([
            'name' => 'Admin Master',
            'email' => 'admin@example.com',
        ]);

        // Usuários normais
        User::factory(10)->create();
    }
}

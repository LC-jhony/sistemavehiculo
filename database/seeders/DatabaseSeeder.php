<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Administrador',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
        ]);
        User::factory()->create([
            'name' => 'Super Administrador',
            'email' => 'superadmin@example.com',
            'password' => Hash::make('password'),
        ]);
        User::factory()->create([
            'name' => 'Usuario',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
        ]);
    }
}

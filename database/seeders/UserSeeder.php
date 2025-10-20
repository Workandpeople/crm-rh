<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        User::factory()->superadmin()->create([
            'name' => 'Lucas SuperAdmin',
            'email' => 'lucas@workandpeople.fr',
            'password' => Hash::make('superadmin123'),
        ]);

        // Admin
        User::factory()->admin()->create([
            'name' => 'Myriam Admin',
            'email' => 'myriam@geniuscontrole.fr',
            'password' => Hash::make('admin123'),
        ]);

        // Employés
        User::factory()->user()->create([
            'name' => 'random Employee',
            'email' => 'random@workandpeople.fr',
            'password' => Hash::make('admin123'),
        ]);
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\DossierRH;

class DossierRHSeeder extends Seeder
{
    public function run(): void
    {
        foreach (User::where('role', 'user')->get() as $user) {
            DossierRH::factory()->create([
               'user_id' => $user->id,
            ]);
        }
    }
}

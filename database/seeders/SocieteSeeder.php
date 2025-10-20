<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Societe;

class SocieteSeeder extends Seeder
{
    public function run(): void
    {
        Societe::create([
            'nom' => 'Work And People',
            'domaine_email' => 'workandpeople.fr',
            'logo' => 'images/workandpeople.png',
        ]);

        Societe::create([
            'nom' => 'Genius Contrôle',
            'domaine_email' => 'geniuscontrole.fr',
            'logo' => 'images/geniuscontrole.png',
        ]);
    }
}

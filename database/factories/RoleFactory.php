<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    public function definition()
    {
        return [
            'name' => $this->faker->unique()->randomElement([
                'superadmin',   // Dev + client (accès total multi-sociétés)
                'admin',        // RH et patrons d’entreprise
                'chef_equipe',  // Managers intermédiaires
                'employe',      // Salariés standards
            ]),
            'label' => $this->faker->randomElement([
                'Super administrateur',
                'Administrateur',
                'Chef d’équipe',
                'Employé',
            ]),
        ];
    }
}

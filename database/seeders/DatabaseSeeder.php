<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\{
    Role,
    Company,
    Team,
    User,
    EmployeeProfile,
    Document,
    Vehicle,
    Epi,
    Leave,
    Overtime,
    Review,
    Ticket,
    TicketComment,
    Task,
    Expense,
    Balance,
    BlogPost,
    Notification
};

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // === RÔLES FIXES ===
        $roles = collect([
            ['name' => 'superadmin', 'label' => 'Super administrateur'],
            ['name' => 'admin', 'label' => 'Administrateur'],
            ['name' => 'chef_equipe', 'label' => 'Chef d’équipe'],
            ['name' => 'employe', 'label' => 'Employé'],
        ])->map(fn($r) => Role::firstOrCreate(['name' => $r['name']], $r));

        // === SOCIÉTÉS ===
        $work = Company::factory()->create([
            'name'    => 'Work And People',
            'domain'  => '@workandpeople.fr',
            'email'   => 'contact@workandpeople.fr',
            'phone'   => '04 66 12 45 78',
            'address' => '12 Rue du Travail, 34000 Montpellier',
        ]);

        $genius = Company::factory()->create([
            'name'    => 'Genius Contrôle',
            'domain'  => '@geniuscontrole.fr',
            'email'   => 'contact@geniuscontrole.fr',
            'phone'   => '04 67 25 98 32',
            'address' => '24 Avenue de la Technologie, 13000 Marseille',
        ]);

        // === SUPER ADMIN GLOBAL ===
        $lucas = User::factory()->create([
            'id'          => Str::uuid(),
            'first_name'  => 'Lucas',
            'last_name'   => 'Dinnichert',
            'email'       => 'lucas@crm.dev',
            'password'    => Hash::make('Wap92!'),
            'company_id'  => $work->id,
            'role_id'     => $roles->firstWhere('name', 'superadmin')->id,
            'status'      => 'active',
        ]);

        // === ADMIN PRINCIPAL SOCIÉTÉ ===
        $myriam = User::factory()->create([
            'id'          => Str::uuid(),
            'first_name'  => 'Myriam',
            'last_name'   => 'Admin',
            'email'       => 'myriam@workandpeople.fr',
            'password'    => Hash::make('Wap92!'),
            'company_id'  => $work->id,
            'role_id'     => $roles->firstWhere('name', 'admin')->id,
            'status'      => 'active',
        ]);

        // === LIAISON ADMIN ↔ SOCIÉTÉ ===
        $work->update(['admin_user_id' => $myriam->id]);
        $genius->update(['admin_user_id' => $lucas->id]);

        // === ÉQUIPES + UTILISATEURS ===
        Company::all()->each(function ($company) use ($roles) {
            $teams = Team::factory(2)->for($company)->create();

            $teams->each(function ($team) use ($roles, $company) {
                // Création du chef d’équipe
                $leader = User::factory()->create([
                    'id'          => Str::uuid(),
                    'first_name'  => fake()->firstName(),
                    'last_name'   => fake()->lastName(),
                    'email'       => fake()->unique()->safeEmail(),
                    'team_id'     => $team->id,
                    'company_id'  => $company->id,
                    'role_id'     => $roles->firstWhere('name', 'chef_equipe')->id,
                    'status'      => 'active',
                    'password'    => Hash::make('Wap92!'),
                ]);

                // Mise à jour du lien chef → équipe
                $team->update(['leader_user_id' => $leader->id]);

                // Employés
                User::factory(5)->create([
                    'team_id'     => $team->id,
                    'company_id'  => $company->id,
                    'role_id'     => $roles->firstWhere('name', 'employe')->id,
                    'status'      => 'active',
                    'password'    => Hash::make('Wap92!'),
                ]);
            });
        });

        // === MODULES RH ===
        EmployeeProfile::factory(15)->create();
        Document::factory(40)->create();
        Vehicle::factory(5)->create();
        Epi::factory(20)->create();
        Leave::factory(20)->create();
        Overtime::factory(10)->create();
        Review::factory(10)->create();

        // === TICKETS & TÂCHES ===
        $tickets = Ticket::factory(15)->create();
        TicketComment::factory(30)->create();
        $tasks = Task::factory(15)->create();

        // Association aléatoire ticket ↔ tâche
        foreach ($tickets as $ticket) {
            $ticket->tasks()->attach($tasks->random(rand(1, 3))->pluck('id'));
        }

        // === FINANCES & COMMUNICATION ===
        Expense::factory(20)->create();
        Balance::factory(3)->create();
        BlogPost::factory(6)->create();
        Notification::factory(20)->create();

        // === LOG FINAL ===
        echo "\n✅ Base de données peuplée avec succès.\n";
        echo "   → Sociétés : " . Company::count() . "\n";
        echo "   → Utilisateurs : " . User::count() . "\n";
        echo "   → Équipes : " . Team::count() . "\n";
    }
}

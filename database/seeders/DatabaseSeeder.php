<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{
    Role, Company, Team, User, EmployeeProfile, Document, Vehicle,
    Epi, Leave, Overtime, Review, Ticket, TicketComment, Task,
    Expense, Balance, BlogPost, Notification
};

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // === Rôles fixes ===
        $roles = collect([
            ['name' => 'superadmin', 'label' => 'Super administrateur'],
            ['name' => 'admin', 'label' => 'Administrateur'],
            ['name' => 'chef_equipe', 'label' => 'Chef d’équipe'],
            ['name' => 'employe', 'label' => 'Employé'],
        ])->map(fn($r) => Role::firstOrCreate(['name' => $r['name']], $r));

        // === Sociétés principales ===
        $work = Company::factory()->create([
            'name' => 'Work And People',
            'domain' => '@workandpeople.fr',
        ]);

        $genius = Company::factory()->create([
            'name' => 'Genius Contrôle',
            'domain' => '@geniuscontrole.fr',
        ]);

        // === Super admin (client ou dev) ===
        User::factory()->create([
            'first_name'  => 'Lucas',
            'last_name'   => 'Dev',
            'email'       => 'lucas@crm.dev',
            'company_id'  => $work->id,
            'role_id'     => $roles->firstWhere('name', 'superadmin')->id,
            'status'      => 'active',
        ]);

        // === Admin principal ===
        User::factory()->create([
            'first_name'  => 'Myriam',
            'last_name'   => 'Admin',
            'email'       => 'myriam@workandpeople.fr',
            'company_id'  => $work->id,
            'role_id'     => $roles->firstWhere('name', 'admin')->id,
            'status'      => 'active',
        ]);

        // === Équipes et utilisateurs ===
        Company::all()->each(function ($company) use ($roles) {
            $teams = Team::factory(2)->for($company)->create();

            $teams->each(function ($team) use ($roles, $company) {
                // Chef d’équipe
                User::factory()->create([
                    'team_id'     => $team->id,
                    'company_id'  => $company->id,
                    'role_id'     => $roles->firstWhere('name', 'chef_equipe')->id,
                    'status'      => 'active',
                ]);

                // Employés
                User::factory(5)->create([
                    'team_id'     => $team->id,
                    'company_id'  => $company->id,
                    'role_id'     => $roles->firstWhere('name', 'employe')->id,
                    'status'      => 'active',
                ]);
            });
        });

        // === Modules RH ===
        EmployeeProfile::factory(15)->create();
        Document::factory(40)->create();
        Vehicle::factory(5)->create();
        Epi::factory(20)->create();
        Leave::factory(20)->create();
        Overtime::factory(10)->create();
        Review::factory(10)->create();

        // === Tickets & tâches ===
        $tickets = Ticket::factory(15)->create();
        TicketComment::factory(30)->create();
        $tasks = Task::factory(15)->create();

        foreach ($tickets as $ticket) {
            $ticket->tasks()->attach($tasks->random(rand(1, 3))->pluck('id'));
        }

        // === Finances & communication ===
        Expense::factory(20)->create();
        Balance::factory(3)->create();
        BlogPost::factory(6)->create();
        Notification::factory(20)->create();
    }
}

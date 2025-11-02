<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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
        $password = Hash::make('Velizy78@');

        // === RÔLES DE BASE ===
        $roles = collect([
            ['name' => 'superadmin', 'label' => 'Super administrateur'],
            ['name' => 'admin', 'label' => 'Administrateur'],
            ['name' => 'chef_equipe', 'label' => 'Chef d’équipe'],
            ['name' => 'employe', 'label' => 'Employé'],
        ])->map(fn($r) => Role::firstOrCreate(['name' => $r['name']], $r));
        $rolesByName = $roles->keyBy('name');

        // === SUPER ADMIN GLOBAL ===
        $superadmin = User::factory()->create([
            'id'          => Str::uuid(),
            'first_name'  => 'Lucas',
            'last_name'   => 'Dinnichert',
            'email'       => 'contact@lucas-dinnichert.fr',
            'password'    => $password,
            'status'      => 'active',
            'role_id'     => $rolesByName['superadmin']->id,
        ]);

        // === ENTREPRISES ===
        $companies = collect([
            ['name' => 'Work And People', 'domain' => '@workandpeople.fr', 'city' => 'Montpellier'],
            ['name' => 'Genius Contrôle', 'domain' => '@geniuscontrole.fr', 'city' => 'Marseille'],
            ['name' => 'EcoLab Services', 'domain' => '@ecolab.fr', 'city' => 'Toulouse'],
        ])->map(function ($c) {
            // ⚠️ On utilise createQuietly() pour éviter les callbacks factory
            return Company::create([
                'id'       => Str::uuid(),
                'name'     => $c['name'],
                'domain'   => $c['domain'],
                'email'    => 'contact' . $c['domain'],
                'phone'    => fake()->phoneNumber(),
                'address'  => fake()->streetAddress() . ', ' . $c['city'],
            ]);
        });

        $ticketTypes = ['conge', 'note_frais', 'incident', 'autre'];
        $ticketStatuses = ['en_attente', 'valide', 'refuse'];
        $ticketPriorities = ['basse', 'moyenne', 'haute'];

        // === ORGANISATION PAR ENTREPRISE ===
        $companies->each(function ($company) use ($rolesByName, $password, $ticketTypes, $ticketStatuses, $ticketPriorities) {
            $companyUsers = collect();

            // Admin référent
            $admin = User::factory()->create([
                'first_name'  => fake()->firstName(),
                'last_name'   => 'Admin',
                'email'       => 'admin' . $company->domain,
                'password'    => $password,
                'company_id'  => $company->id,
                'role_id'     => $rolesByName['admin']->id,
                'status'      => 'active',
            ]);
            $company->update(['admin_user_id' => $admin->id]);
            $companyUsers->push($admin);

            // 5 équipes par société
            $teams = Team::factory()->count(5)->create(['company_id' => $company->id]);

            $teams->each(function ($team) use ($rolesByName, $company, $password, $companyUsers) {
                $leader = User::factory()->create([
                    'company_id' => $company->id,
                    'team_id'    => $team->id,
                    'role_id'    => $rolesByName['chef_equipe']->id,
                    'password'   => $password,
                    'status'     => 'active',
                ]);

                $team->update(['leader_user_id' => $leader->id]);

                $companyUsers->push($leader);

                $employees = User::factory()->count(5)->create([
                    'company_id' => $company->id,
                    'team_id'    => $team->id,
                    'role_id'    => $rolesByName['employe']->id,
                    'password'   => $password,
                    'status'     => 'active',
                ]);

                $employees->each(function ($employee) use ($companyUsers) {
                    $companyUsers->push($employee);
                });
            });

            $teamMembers = $companyUsers->whereNotNull('team_id')->values();
            $vehiclePool = Vehicle::factory()->count(6)->create();

            // RH : profils, documents, EPI, congés, heures sup, entretiens
            $teamMembers->each(function ($user) use ($company, $vehiclePool) {
                $assignedVehicleId = null;
                if ($vehiclePool->isNotEmpty() && fake()->boolean(35)) {
                    $vehicle = $vehiclePool->pop();
                    $vehicle->update(['assigned_to' => $user->id]);
                    $assignedVehicleId = $vehicle->id;
                }

                EmployeeProfile::factory()->create([
                    'user_id'    => $user->id,
                    'vehicle_id' => $assignedVehicleId,
                    'position'   => fake()->jobTitle(),
                ]);

                $documentTypes = collect(['CNI', 'Contrat', 'Carte Vitale', 'Fiche Fonction'])
                    ->shuffle()
                    ->take(rand(2, 4));

                $documentTypes->each(fn($type) =>
                    Document::factory()->create([
                        'user_id' => $user->id,
                        'type'    => $type,
                    ])
                );

                Epi::factory()->count(rand(1, 2))->create([
                    'user_id' => $user->id,
                ]);

                $leaveCount = rand(1, 2);
                Leave::factory()->count($leaveCount)->create([
                    'user_id' => $user->id,
                ]);

                Overtime::factory()->count(rand(0, 2))->create([
                    'user_id' => $user->id,
                ]);

                Review::factory()->create([
                    'user_id' => $user->id,
                ]);

                Expense::factory()->count(rand(1, 3))->create([
                    'user_id'    => $user->id,
                    'company_id' => $company->id,
                ]);

                Notification::factory()->count(rand(1, 3))->create([
                    'user_id' => $user->id,
                ]);
            });

            Balance::factory()->create(['company_id' => $company->id]);

            BlogPost::factory()->count(3)->create([
                'company_id' => $company->id,
                'created_by' => $admin->id,
            ]);

            Notification::factory()->count(rand(1, 2))->create([
                'user_id' => $admin->id,
            ]);

            // Tickets & tâches cohérents par société
            $companyTickets = collect();
            foreach (range(1, 25) as $i) {
                $creator = $companyUsers->random();
                $assigneePool = $companyUsers->where('id', '!=', $creator->id);
                $assignee = $assigneePool->isNotEmpty()
                    ? $assigneePool->random()
                    : $creator;

                $ticket = Ticket::factory()->create([
                    'company_id'  => $company->id,
                    'created_by'  => $creator->id,
                    'assigned_to' => $assignee->id,
                    'type'        => fake()->randomElement($ticketTypes),
                    'priority'    => fake()->randomElement($ticketPriorities),
                    'status'      => fake()->randomElement($ticketStatuses),
                ]);

                $companyTickets->push($ticket);
            }

            $companyTasks = collect();
            foreach (range(1, 20) as $i) {
                $creator = $companyUsers->random();
                $assigneePool = $companyUsers->where('id', '!=', $creator->id);
                $assignee = $assigneePool->isNotEmpty()
                    ? $assigneePool->random()
                    : $creator;

                $companyTasks->push(
                    Task::factory()->create([
                        'company_id'  => $company->id,
                        'created_by'  => $creator->id,
                        'assigned_to' => $assignee->id,
                    ])
                );
            }

            $companyTickets->each(function ($ticket) use ($companyTasks, $companyUsers) {
                $tasksToAttach = $companyTasks->shuffle()->take(rand(1, min(4, $companyTasks->count())))->pluck('id');
                if ($tasksToAttach->isNotEmpty()) {
                    $ticket->tasks()->syncWithoutDetaching($tasksToAttach->all());
                }

                foreach (range(1, rand(2, 4)) as $i) {
                    TicketComment::factory()->create([
                        'ticket_id' => $ticket->id,
                        'user_id'   => $companyUsers->random()->id,
                    ]);
                }
            });
        });

        Notification::factory()->count(5)->create([
            'user_id' => $superadmin->id,
        ]);

        // === LOG FINAL ===
        echo "\n✅ Base de données peuplée avec succès.\n";
        echo "   → Sociétés : " . Company::count() . "\n";
        echo "   → Utilisateurs : " . User::count() . "\n";
        echo "   → Équipes : " . Team::count() . "\n";
        echo "   → Tickets : " . Ticket::count() . "\n";
        echo "   → Tâches : " . Task::count() . "\n";
    }
}

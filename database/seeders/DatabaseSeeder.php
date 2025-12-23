<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Carbon\Carbon;
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
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $faker = fake();
        $password = Hash::make('Velizy78@');

        // === RÔLES DE BASE ===
        $rolesByName = collect([
            ['name' => 'superadmin', 'label' => 'Super administrateur'],
            ['name' => 'admin', 'label' => 'Administrateur'],
            ['name' => 'chef_equipe', 'label' => 'Chef d’équipe'],
            ['name' => 'employe', 'label' => 'Employé'],
        ])->mapWithKeys(fn($role) => [
            $role['name'] => Role::firstOrCreate(['name' => $role['name']], $role)
        ]);

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

        // === ENTREPRISES & DONNÉES COHÉRENTES ===
        $companiesSeed = [
            [
                'name' => 'Work And People',
                'domain' => '@workandpeople.fr',
                'city' => 'Montpellier',
                'teams' => ['Installation', 'Support', 'QSE'],
            ],
            [
                'name' => 'Genius Controle',
                'domain' => '@geniuscontrole.fr',
                'city' => 'Marseille',
                'teams' => ['Production', 'Maintenance', 'Projets'],
            ],
        ];

        foreach ($companiesSeed as $companyData) {
            $company = Company::create([
                'id'       => Str::uuid(),
                'name'     => $companyData['name'],
                'domain'   => $companyData['domain'],
                'email'    => 'contact' . $companyData['domain'],
                'phone'    => $faker->phoneNumber(),
                'address'  => $faker->streetAddress() . ', ' . $companyData['city'],
                'policies_json' => [
                    'max_conges' => 25,
                    'auto_validation' => false,
                ],
            ]);

            $companyUsers = collect();

            // Admin référent
            $admin = User::factory()->create([
                'first_name'  => $faker->firstName(),
                'last_name'   => 'Admin',
                'email'       => 'admin' . $company->domain,
                'password'    => $password,
                'company_id'  => $company->id,
                'role_id'     => $rolesByName['admin']->id,
                'status'      => 'active',
            ]);
            $company->update(['admin_user_id' => $admin->id]);
            $companyUsers->push($admin);

            // Équipes et managers
            $teams = collect($companyData['teams'])->map(fn($teamName) => Team::factory()->create([
                'company_id' => $company->id,
                'name'       => $teamName,
            ]));

            $teams->each(function (Team $team) use (&$companyUsers, $rolesByName, $company, $password, $faker) {
                $leader = User::factory()->create([
                    'company_id' => $company->id,
                    'team_id'    => $team->id,
                    'role_id'    => $rolesByName['chef_equipe']->id,
                    'password'   => $password,
                    'status'     => 'active',
                    'email'      => Str::slug($faker->firstName() . '.' . $faker->lastName()) . '-' . Str::random(4) . $company->domain,
                ]);

                $team->update(['leader_user_id' => $leader->id]);
                $companyUsers->push($leader);

                $employees = User::factory()->count(4)->create([
                    'company_id' => $company->id,
                    'team_id'    => $team->id,
                    'role_id'    => $rolesByName['employe']->id,
                    'password'   => $password,
                    'status'     => 'active',
                ])->each(function ($employee) use (&$companyUsers, $company, $faker) {
                    $employee->update([
                        'email' => Str::slug($employee->first_name . '.' . $employee->last_name) . '-' . Str::random(4) . $company->domain,
                    ]);
                    $companyUsers->push($employee);
                });
            });

            $teamMembers = $companyUsers->whereNotNull('team_id')->values();
            $vehiclePool = Vehicle::factory()->count(5)->create();
            $expenseTotal = 0;

            // RH : profils, documents, EPI, congés, heures sup, entretiens
            $teamMembers->each(function ($user) use (&$expenseTotal, $vehiclePool, $company, $admin, $faker) {
                $assignedVehicleId = null;
                if ($vehiclePool->isNotEmpty() && $faker->boolean(35)) {
                    $vehicle = $vehiclePool->pop();
                    $vehicle->update(['assigned_to' => $user->id]);
                    $assignedVehicleId = $vehicle->id;
                }

                EmployeeProfile::factory()->create([
                    'user_id'    => $user->id,
                    'vehicle_id' => $assignedVehicleId,
                    'position'   => $faker->jobTitle(),
                ]);

                $documentTypes = collect(['CNI', 'Contrat', 'Carte Vitale', 'Fiche Fonction'])
                    ->shuffle()
                    ->take(rand(2, 3));

                $documentTypes->each(function ($type) use ($user, $faker) {
                    Document::factory()->create([
                        'user_id'    => $user->id,
                        'type'       => $type,
                        'status'     => $faker->randomElement(['validated', 'pending', 'expired']),
                        'expires_at' => now()->addMonths(rand(6, 18)),
                        'signed'     => true,
                        'signed_at'  => now()->subDays(rand(3, 30)),
                    ]);
                });

                Epi::factory()->count(rand(1, 2))->create([
                    'user_id' => $user->id,
                ]);

                Leave::factory()->create([
                    'user_id'      => $user->id,
                    'validated_by' => $admin->id,
                    'status'       => $faker->randomElement(['pending', 'approved', 'rejected']),
                ]);

                Overtime::factory()->create([
                    'user_id'      => $user->id,
                    'validated_by' => $admin->id,
                    'status'       => $faker->randomElement(['pending', 'approved']),
                ]);

                Review::factory()->create([
                    'user_id'      => $user->id,
                    'scheduled_at' => now()->addMonths(rand(1, 6)),
                    'status'       => $faker->randomElement(['pending', 'done']),
                ]);

                $expenses = Expense::factory()->count(rand(1, 3))->create([
                    'user_id'      => $user->id,
                    'company_id'   => $company->id,
                    'status'       => $faker->randomElement(['pending', 'approved', 'paid']),
                    'validated_by' => $admin->id,
                ]);
                $expenseTotal += $expenses->sum('amount');

                Notification::factory()->count(rand(1, 2))->create([
                    'user_id' => $user->id,
                    'type'    => 'info',
                ]);
            });

            $companyTasks = collect();
            foreach (range(1, 8) as $i) {
                $creator = $companyUsers->random();
                $assignee = $companyUsers->random();

                $companyTasks->push(
                    Task::factory()->create([
                        'company_id'  => $company->id,
                        'created_by'  => $creator->id,
                        'assigned_to' => $assignee->id,
                        'priority'    => $faker->randomElement(['low', 'medium', 'high']),
                        'status'      => $faker->randomElement(['todo', 'doing', 'review', 'done']),
                        'due_date'    => now()->addDays(rand(5, 20)),
                    ])
                );
            }

            $ticketTypes = ['conge', 'note_frais', 'document_rh', 'incident', 'autre'];
            $ticketStatuses = ['en_attente', 'valide', 'refuse'];
            $ticketPriorities = ['basse', 'moyenne', 'haute'];

            $companyTickets = collect();
            foreach (range(1, 12) as $i) {
                $creator = $companyUsers->random();
                $assigneePool = $companyUsers->where('id', '!=', $creator->id);
                $assignee = $assigneePool->isNotEmpty()
                    ? $assigneePool->random()
                    : $creator;

                $type = $faker->randomElement($ticketTypes);
                $details = match ($type) {
                    'conge' => [
                        'start_date' => now()->addDays(rand(3, 15))->toDateString(),
                        'end_date'   => now()->addDays(rand(16, 25))->toDateString(),
                        'reason'     => $faker->sentence(),
                    ],
                    'note_frais' => [
                        'amount' => $faker->randomFloat(2, 20, 250),
                        'category' => $faker->randomElement(['repas', 'peage', 'hebergement', 'km']),
                        'receipt_path' => 'storage/receipts/' . Str::uuid() . '.pdf',
                    ],
                    'document_rh' => [
                        'document_type' => $faker->randomElement(['Contrat', 'Avenant', 'Certificat']),
                        'needs_signature' => true,
                    ],
                    'incident' => [
                        'location' => $faker->city(),
                        'severity' => $faker->randomElement(['mineur', 'majeur']),
                        'impact' => $faker->sentence(),
                    ],
                    default => ['note' => $faker->sentence()],
                };

                $ticket = Ticket::factory()->create([
                    'company_id'     => $company->id,
                    'created_by'     => $creator->id,
                    'assigned_to'    => $assignee->id,
                    'type'           => $type,
                    'title'          => ucfirst($faker->words(3, true)),
                    'description'    => $faker->paragraph(),
                    'priority'       => $faker->randomElement($ticketPriorities),
                    'status'         => $faker->randomElement($ticketStatuses),
                    'due_date'       => now()->addDays(rand(2, 12)),
                    'details'        => $details,
                    'related_user_id'=> $faker->boolean(50) ? $companyUsers->random()->id : null,
                ]);

                $companyTickets->push($ticket);
            }

            $companyTickets->each(function ($ticket) use ($companyTasks, $companyUsers, $faker) {
                $tasksToAttach = $companyTasks->shuffle()->take(rand(1, min(3, $companyTasks->count())))->pluck('id');
                if ($tasksToAttach->isNotEmpty()) {
                    $ticket->tasks()->syncWithoutDetaching($tasksToAttach->all());
                }

                foreach (range(1, rand(1, 3)) as $i) {
                    TicketComment::factory()->create([
                        'ticket_id' => $ticket->id,
                        'user_id'   => $companyUsers->random()->id,
                        'content'   => $faker->sentences(2, true),
                    ]);
                }
            });

            // Congés alignés sur la table dédiée
            $congesPayload = collect(range(1, 2))->map(function () use ($company, $teamMembers, $admin, $faker) {
                $user = $teamMembers->random();
                $start = Carbon::now()->addDays(rand(4, 18));
                $end = (clone $start)->addDays(rand(2, 5));

                return [
                    'id' => Str::uuid(),
                    'company_id' => $company->id,
                    'user_id' => $user->id,
                    'ticket_id' => null,
                    'type' => $faker->randomElement(['conges_payes', 'rtt', 'autre']),
                    'start_date' => $start->toDateString(),
                    'end_date' => $end->toDateString(),
                    'days_count' => $start->diffInDays($end) + 1,
                    'status' => $faker->randomElement(['en_attente', 'valide']),
                    'reason' => $faker->sentence(),
                    'admin_comment' => null,
                    'validated_by' => $faker->boolean(50) ? $admin->id : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            });

            DB::table('conges')->insert($congesPayload->all());

            Balance::updateOrCreate(
                ['company_id' => $company->id],
                [
                    'total_expenses' => round($expenseTotal, 2),
                    'total_incomes'  => $faker->randomFloat(2, 20000, 80000),
                ]
            );

            BlogPost::factory()->count(2)->create([
                'company_id' => $company->id,
                'created_by' => $admin->id,
            ]);

            Notification::factory()->count(rand(1, 2))->create([
                'user_id' => $admin->id,
                'type'    => 'alert',
            ]);
        }

        Notification::factory()->count(5)->create([
            'user_id' => $superadmin->id,
            'type'    => 'alert',
        ]);
    }
}

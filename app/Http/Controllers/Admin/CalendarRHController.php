<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class CalendarRHController extends Controller
{
    /**
     * Retourne les événements du calendrier RH (V1 = congés approuvés).
     *
     * GET /admin/calendar-rh/events?month=YYYY-MM&company_id=&team_id=
     */
    public function events(Request $request)
    {
        try {
            $user = Auth::user();
            $roleName = $user->role->name ?? null;

            if (!in_array($roleName, ['admin', 'chef_equipe', 'superadmin'])) {
                return response()->json(['message' => 'Accès non autorisé'], 403);
            }

            // company / team comme partout ailleurs
            $companyId = $request->query('company_id', $user->company_id);
            $teamId    = $request->query('team_id');

            // Mois au format YYYY-MM, sinon mois courant
            $monthParam = $request->query('month');
            if ($monthParam) {
                try {
                    $startOfMonth = Carbon::createFromFormat('Y-m', $monthParam)->startOfMonth();
                } catch (\Throwable $e) {
                    $startOfMonth = now()->startOfMonth();
                }
            } else {
                $startOfMonth = now()->startOfMonth();
            }
            $endOfMonth = (clone $startOfMonth)->endOfMonth();

            Log::info('[CalendarRHController@events] Chargement calendrier', [
                'user_id'      => $user->id,
                'role'         => $roleName,
                'company_id'   => $companyId,
                'team_id'      => $teamId,
                'start_month'  => $startOfMonth->toDateString(),
                'end_month'    => $endOfMonth->toDateString(),
            ]);

            // On ne garde que les congés approuvés
            $query = Leave::with(['user'])
                ->where('status', 'approved')
                ->whereHas('user', function ($q) use ($companyId, $teamId) {
                    $q->where('company_id', $companyId);
                    if ($teamId) {
                        $q->where('team_id', $teamId);
                    }
                })
                // qui se recoupent avec [startOfMonth, endOfMonth]
                ->where(function ($q) use ($startOfMonth, $endOfMonth) {
                    $q->whereBetween('start_date', [$startOfMonth, $endOfMonth])
                      ->orWhereBetween('end_date', [$startOfMonth, $endOfMonth])
                      ->orWhere(function ($qq) use ($startOfMonth, $endOfMonth) {
                          $qq->where('start_date', '<=', $startOfMonth)
                             ->where('end_date', '>=', $endOfMonth);
                      });
                });

            $leaves = $query->get();

            $events = [];

            foreach ($leaves as $leave) {
                $current = Carbon::parse($leave->start_date)->max($startOfMonth);
                $last    = Carbon::parse($leave->end_date)->min($endOfMonth);

                $eventType = $leave->type === 'Maladie' ? 'maladie' : 'conge';

                $user = $leave->user;
                $fullName = $user
                    ? trim($user->first_name . ' ' . $user->last_name)
                    : 'Employé inconnu';

                while ($current->lte($last)) {
                    $events[] = [
                        // ID unique par jour de congé (évite les collisions FullCalendar)
                        'id'    => $leave->id . '-' . $current->toDateString(),
                        'title' => $fullName,
                        'start' => $current->toDateString(),
                        'allDay'=> true,
                        'extendedProps' => [
                            'type'     => $eventType,      // 'conge' ou 'maladie'
                            'raw_type' => $leave->type,    // CP, SansSolde, Exceptionnel, Maladie
                            'user'     => $user ? [
                                'id'         => $user->id,
                                'first_name' => $user->first_name,
                                'last_name'  => $user->last_name,
                            ] : null,
                        ],
                    ];

                    $current->addDay();
                }
            }


            return response()->json([
                'events' => $events,
                'meta'   => [
                    'month' => $startOfMonth->format('Y-m'),
                    'start' => $startOfMonth->toDateString(),
                    'end'   => $endOfMonth->toDateString(),
                ],
            ]);

        } catch (\Throwable $e) {
            Log::error('[CalendarRHController@events] Erreur', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'message' => 'Erreur serveur',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
}

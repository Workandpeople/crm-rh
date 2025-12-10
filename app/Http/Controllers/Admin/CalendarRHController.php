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
    public function events(Request $request)
    {
        try {
            $user     = Auth::user();
            $roleName = $user->role->name ?? null;

            if (! in_array($roleName, ['admin', 'chef_equipe', 'superadmin'])) {
                return response()->json(['message' => 'Accès non autorisé'], 403);
            }

            // Company / team comme ailleurs
            $companyId = $request->query('company_id', $user->company_id);
            $teamId    = $request->query('team_id');

            // FullCalendar envoie start / end (ISO)
            $startParam = $request->query('start');
            $endParam   = $request->query('end');

            $rangeStart = $startParam ? Carbon::parse($startParam)->startOfDay() : now()->startOfMonth();
            $rangeEnd   = $endParam   ? Carbon::parse($endParam)->endOfDay()   : now()->endOfMonth();

            Log::info('[CalendarRHController@events] Chargement calendrier', [
                'user_id'    => $user->id,
                'role'       => $roleName,
                'company_id' => $companyId,
                'team_id'    => $teamId,
                'start'      => $rangeStart->toDateString(),
                'end'        => $rangeEnd->toDateString(),
            ]);

            // On ne garde que les congés approuvés qui se recoupent avec la plage demandée
            $query = Leave::with('user')
                ->where('status', 'approved')
                ->whereHas('user', function ($q) use ($companyId, $teamId) {
                    $q->where('company_id', $companyId);
                    if ($teamId) {
                        $q->where('team_id', $teamId);
                    }
                })
                ->where(function ($q) use ($rangeStart, $rangeEnd) {
                    $q->whereBetween('start_date', [$rangeStart, $rangeEnd])
                      ->orWhereBetween('end_date', [$rangeStart, $rangeEnd])
                      ->orWhere(function ($qq) use ($rangeStart, $rangeEnd) {
                          $qq->where('start_date', '<=', $rangeStart)
                             ->where('end_date', '>=', $rangeEnd);
                      });
                });

            $leaves = $query->get();

            $events = [];

            foreach ($leaves as $leave) {
                $user = $leave->user;
                $fullName = $user
                    ? trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? ''))
                    : 'Employé inconnu';

                // Type visuel : maladie vs congé
                $eventType = $leave->type === 'Maladie' ? 'maladie' : 'conge';

                // ⚠️ FullCalendar : end est EXCLU → on ajoute 1 jour pour couvrir correctement la dernière journée
                $start = Carbon::parse($leave->start_date)->toDateString();
                $end   = Carbon::parse($leave->end_date)->addDay()->toDateString();

                $events[] = [
                    'id'    => $leave->id,
                    'title' => $fullName,
                    'start' => $start,
                    'end'   => $end,
                    'allDay'=> true,
                    'extendedProps' => [
                        'type'     => $eventType,      // 'conge' ou 'maladie' -> utilisé par tes classes CSS
                        'raw_type' => $leave->type,    // CP / SansSolde / Exceptionnel / Maladie...
                        'user'     => $user ? [
                            'id'         => $user->id,
                            'first_name' => $user->first_name,
                            'last_name'  => $user->last_name,
                        ] : null,
                    ],
                ];
            }

            return response()->json([
                'events' => $events,
                'meta'   => [
                    'start' => $rangeStart->toDateString(),
                    'end'   => $rangeEnd->toDateString(),
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

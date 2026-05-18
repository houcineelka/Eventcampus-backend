<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Event;
use App\Models\EventParticipation;
use App\Models\User;

class StatsController extends Controller
{
    public function index()
    {
        $totalEvents    = Event::count();
        $publishedEvents = Event::where('status', 'valide')->count();
        $totalClubs     = Club::count();
        $activeClubs    = Club::where('status', 'actif')->count();
        $totalStudents  = User::where('role', 'etudiant')->count();
        $totalCapacity  = Event::where('status', 'valide')->sum('max_participants');
        $totalRegistrations = EventParticipation::count();

        $avgRate = $totalCapacity > 0
            ? round(($totalRegistrations / $totalCapacity) * 100)
            : 0;

        $recentEvents = Event::with('club')
            ->where('status', 'valide')
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($event) {
                $registrations = $event->participants()->count();
                $rate = $event->max_participants > 0
                    ? round(($registrations / $event->max_participants) * 100)
                    : 0;

                return [
                    'id'            => $event->id,
                    'title'         => $event->title,
                    'club'          => $event->club?->name ?? '—',
                    'date'          => $event->date,
                    'registrations' => $registrations,
                    'capacity'      => $event->max_participants,
                    'rate'          => $rate,
                    'status'        => $event->status,
                ];
            });

        return response()->json([
            'data' => [
                'total_events'       => $publishedEvents,
                'total_clubs'        => $activeClubs,
                'total_students'     => $totalStudents,
                'avg_inscription_rate' => $avgRate,
                'recent_events'      => $recentEvents,
            ],
        ]);
    }
}
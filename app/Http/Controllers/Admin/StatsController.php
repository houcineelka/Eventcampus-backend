<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\Event;
use App\Models\User;

class StatsController extends Controller
{
    public function index()
    {
        $totalEvents     = Event::where('statut', 'valide')->count();
        $totalClubs      = Club::where('statut', 'actif')->count();
        $totalStudents   = User::where('role', 'etudiant')->count();

        $totalCapacity      = Event::where('statut', 'valide')->sum('capacite_max');
        $totalRegistrations = Event::where('statut', 'valide')
            ->withCount('participants')
            ->get()
            ->sum('participants_count');

        $avgRate = $totalCapacity > 0
            ? round(($totalRegistrations / $totalCapacity) * 100)
            : 0;

        $recentEvents = Event::with('club')
            ->where('statut', 'valide')
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($event) {
                $registrations = $event->participants()->count();
                $rate = $event->capacite_max > 0
                    ? round(($registrations / $event->capacite_max) * 100)
                    : 0;

                return [
                    'id'            => $event->id,
                    'title'         => $event->titre,        // titre ✓
                    'club'          => $event->club?->nom ?? '—', // nom ✓
                    'date'          => $event->date,
                    'registrations' => $registrations,
                    'capacity'      => $event->capacite_max, // capacite_max ✓
                    'rate'          => $rate,
                    'status'        => $event->statut,       // statut ✓
                ];
            });

        return response()->json([
            'data' => [
                'total_events'         => $totalEvents,
                'total_clubs'          => $totalClubs,
                'total_students'       => $totalStudents,
                'avg_inscription_rate' => $avgRate,
                'recent_events'        => $recentEvents,
            ],
        ]);
    }
}
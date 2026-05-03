<?php
// Branch 2: Modifier l'endpoint POST /inscriptions pour basculer en liste d'attente si complet
// Fichier: app/Http/Controllers/InscriptionController.php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventWaitlist;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InscriptionController extends Controller
{
    /**
     * POST /api/events/{id}/inscriptions
     * Inscrit l'étudiant ou le place en liste d'attente si l'événement est complet.
     */
    public function store(Request $request, int $id): JsonResponse
    {
        $event = Event::findOrFail($id);
        $user  = Auth::user();

        // ── Vérifications préalables ──────────────────────────────────

        // Déjà inscrit ?
        if ($event->estInscrit($user->id)) {
            return response()->json([
                'message' => 'Vous êtes déjà inscrit à cet événement.',
                'statut'  => 'deja_inscrit',
            ], 409);
        }

        // Déjà en liste d'attente ?
        if ($event->estEnListeAttente($user->id)) {
            $rang = $event->rangListeAttente($user->id);
            return response()->json([
                'message'          => 'Vous êtes déjà dans la liste d\'attente.',
                'statut'           => 'deja_en_attente',
                'waitlist_position' => $rang,
            ], 409);
        }

        // ── Inscription ou liste d'attente ────────────────────────────

        return DB::transaction(function () use ($event, $user) {

            // Cas 1 : des places sont disponibles → inscription directe
            if ($event->places_disponibles > 0) {
                $event->participants()->attach($user->id);

                $event->decrement('places_disponibles');

                return response()->json([
                    'message'            => 'Inscription confirmée.',
                    'statut'             => 'inscrit',
                    'places_disponibles' => $event->fresh()->places_disponibles,
                ], 201);
            }

            // Cas 2 : événement complet → liste d'attente
            $rang = $event->prochainRang();

            EventWaitlist::create([
                'event_id' => $event->id,
                'user_id'  => $user->id,
                'position' => $rang,
                'statut'   => 'en_attente',
            ]);

            return response()->json([
                'message'          => 'L\'événement est complet. Vous avez été ajouté à la liste d\'attente.',
                'statut'           => 'liste_attente',
                'waitlist_position' => $rang,
            ], 201);
        });
    }
    
    /**
     * DELETE /api/events/{id}/inscriptions
     * Annule l'inscription ou quitte la liste d'attente.
     */

    public function destroy(int $id): JsonResponse
        {
            $event   = Event::findOrFail($id);
            $user    = Auth::user();
            $service = app(WaitlistService::class);

            // ── Cas 1 : quitter la liste d'attente ──────────────────────────
            $waitlistEntry = EventWaitlist::where('event_id', $event->id)
                                        ->where('user_id', $user->id)
                                        ->where('statut', 'en_attente')
                                        ->first();

            if ($waitlistEntry) {
                return DB::transaction(function () use ($event, $waitlistEntry) {
                    $rang = $waitlistEntry->position;
                    $waitlistEntry->update(['statut' => 'annule']);

                    // Réindexer les rangs suivants
                    EventWaitlist::where('event_id', $event->id)
                                ->where('statut', 'en_attente')
                                ->where('position', '>', $rang)
                                ->decrement('position');

                    return response()->json([
                        'message' => 'Vous avez quitté la liste d\'attente.',
                        'statut'  => 'quitte_attente',
                    ]);
                });
            }

            // ── Cas 2 : annuler une inscription confirmée ───────────────────
            if (!$event->estInscrit($user->id)) {
                return response()->json(['message' => 'Vous n\'êtes pas inscrit à cet événement.'], 404);
            }

            return DB::transaction(function () use ($event, $user, $service) {
                // Retirer l'inscrit
                $event->participants()->detach($user->id);
                $event->increment('places_disponibles');

                // Automatiquement promouvoir le premier de la liste
                $promu = $service->promouvoirPremier($event);

                return response()->json([
                    'message'        => 'Votre inscription a été annulée.',
                    'statut'         => 'annule',
                    'promu_user_id'  => $promu?->user_id,
                ]);
            });
        }
}

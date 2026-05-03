<?php
// app/Services/WaitlistService.php

namespace App\Services;

use App\Models\Event;
use App\Models\EventWaitlist;
use App\Notifications\PlaceDisponibleNotification;
use App\Notifications\RangMisAJourNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WaitlistService
{
    /**
     * Promouvoir le premier de la liste d'attente après une annulation,
     * puis envoyer l'email de notification à l'étudiant promu.
     */
    public function promouvoirPremier(Event $event): ?EventWaitlist
    {
        $premier = EventWaitlist::where('event_id', $event->id)
                                ->where('statut', 'en_attente')
                                ->orderBy('position')
                                ->lockForUpdate()
                                ->first();

        if (!$premier) {
            Log::info("WaitlistService: aucune personne en attente pour event #{$event->id}");
            return null;
        }

        DB::transaction(function () use ($event, $premier) {

            // 1. Inscrire l'utilisateur promu dans event_user
            $event->participants()->syncWithoutDetaching([$premier->user_id]);

            // 2. Consommer la place libérée
            $event->decrement('places_disponibles');

            // 3. Marquer l'entrée comme promue
            $premier->update([
                'statut'     => 'promu',
                'notifie_at' => now(),
            ]);

            // 4. Réindexer les positions des suivants
            EventWaitlist::where('event_id', $event->id)
                         ->where('statut', 'en_attente')
                         ->where('position', '>', $premier->position)
                         ->decrement('position');

            // 5. Envoyer l'email de notification à l'étudiant promu (en queue)
            $user = $premier->user;
            if ($user) {
                $user->notify(new PlaceDisponibleNotification($event));
                Log::info("WaitlistService: email envoyé à user #{$user->id} pour event #{$event->id}");
            }
        });

        return $premier->fresh();
    }

    /**
     * Notifier in-app tous les membres de la liste d'attente
     * de leur nouveau rang après une réindexation.
     */
    public function notifierChangementRang(Event $event): void
    {
        $enAttente = EventWaitlist::where('event_id', $event->id)
                                  ->where('statut', 'en_attente')
                                  ->orderBy('position')
                                  ->with('user')
                                  ->get();

        foreach ($enAttente as $entry) {
            if ($entry->user) {
                $entry->user->notify(
                    new RangMisAJourNotification($event, $entry->position)
                );
            }
        }
    }
}
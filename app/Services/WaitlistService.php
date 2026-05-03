<?php
namespace App\Services;

use App\Models\Event;
use App\Models\EventWaitlist;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WaitlistService
{
    /**
     * Promouvoir le premier de la liste d'attente après une annulation.
     * Appelé depuis InscriptionController::destroy() dans une transaction.
     */
    public function promouvoirPremier(Event $event): ?EventWaitlist
    {
        // Récupérer le premier en liste (rang le plus bas, statut en_attente)
        $premier = EventWaitlist::where('event_id', $event->id)
                                ->where('statut', 'en_attente')
                                ->orderBy('position')
                                ->lockForUpdate()   // évite les race conditions
                                ->first();

        if (!$premier) {
            Log::info("WaitlistService: aucune personne en attente pour l'event #{$event->id}");
            return null;
        }

        DB::transaction(function () use ($event, $premier) {
            // 1. Inscrire l'utilisateur promu dans event_user
            $event->participants()->syncWithoutDetaching([$premier->user_id]);

            // 2. Décrémenter places_disponibles (elle vient d'être libérée puis ré-attribuée)
            //    Note : l'increment a déjà été fait dans destroy(), on le remet à 0
            $event->decrement('places_disponibles');

            // 3. Marquer l'entrée en liste comme promue
            $premier->update([
                'statut'      => 'promu',
                'notifie_at'  => now(),
            ]);

            // 4. Réindexer les positions des suivants
            EventWaitlist::where('event_id', $event->id)
                         ->where('statut', 'en_attente')
                         ->where('position', '>', $premier->position)
                         ->decrement('position');

            Log::info("WaitlistService: user #{$premier->user_id} promu pour event #{$event->id}");
        });

        return $premier->fresh();
    }
}
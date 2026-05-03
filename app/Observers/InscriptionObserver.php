<?php
// app/Observers/InscriptionObserver.php

namespace App\Observers;

use App\Models\Event;
use App\Models\User;
use App\Services\WaitlistService;
use Illuminate\Support\Facades\Log;

/**
 * Observer sur le modèle pivot EventUser.
 * Déclenché automatiquement quand un participant est retiré d'un événement.
 *
 * NB : Laravel déclenche les observers sur les pivots custom (using(EventUser::class))
 * uniquement si le modèle pivot étend Pivot et non MorphPivot.
 */
class InscriptionObserver
{
    public function __construct(private WaitlistService $waitlistService) {}

    /**
     * Appelé après qu'un EventUser pivot est supprimé (participant retiré).
     * La promotion du premier de la liste est déléguée au WaitlistService.
     */
    public function deleted(Event $event): void
    {
        Log::info("InscriptionObserver@deleted — event #{$event->id}");
        $this->waitlistService->promouvoirPremier($event);
    }
}
<?php
// app/Models/EventUser.php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Services\WaitlistService;
use Illuminate\Support\Facades\Log;

class EventUser extends Pivot
{
    protected $table = 'event_user';

    protected static function booted(): void
    {
        static::deleted(function (EventUser $pivot) {
            $event = Event::find($pivot->event_id);

            if (!$event) {
                Log::warning("EventUser deleted: event #{$pivot->event_id} introuvable.");
                return;
            }

            Log::info("EventUser deleted: user #{$pivot->user_id} retiré de event #{$event->id}");

            app(WaitlistService::class)->promouvoirPremier($event);
        });
    }
}
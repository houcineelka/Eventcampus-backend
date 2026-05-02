<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventWaitlist extends Model
{
    protected $table = 'event_waitlist';

    protected $fillable = [
        'event_id',
        'user_id',
        'position',
        'statut',
        'notifie_at',
    ];

    protected $casts = [
        'notifie_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Conversation extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'creator_id',
        'name',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'conversation_user')
                    ->withTimestamps();
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}

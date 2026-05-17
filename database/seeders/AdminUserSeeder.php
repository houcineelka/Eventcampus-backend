<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        if (!User::where('email', 'admin@eventcampus.com')->exists()) {
            User::create([
                'prenom' => 'Admin',
                'nom' => 'EventCampus',
                'name' => 'Admin EventCampus',
                'email' => 'admin@eventcampus.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'email_reminders' => false,
            ]);
        }
    }
}

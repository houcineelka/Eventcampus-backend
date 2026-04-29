<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SendRappelEmails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rappel:send';

    protected $description = 'Envoie les emails de rappel aux étudiants inscrits aux événements du lendemain';

    public function handle()
    {
        $this->info('Envoi des emails de rappel...');
        // La logique sera implémentée dans EP-76 et EP-77
        $this->info('Terminé.');
    }
}

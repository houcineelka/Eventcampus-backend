<?php

namespace App\Services;

use Twilio\Rest\Client;

class WhatsAppService
{
    private Client $client;
    private string $from;

    public function __construct()
    {
        $this->client = new Client(
            config('services.twilio.sid'),
            config('services.twilio.token')
        );
        $this->from = config('services.twilio.whatsapp_from');
    }

    public function send(string $to, string $message): void
    {
        $this->client->messages->create(
            'whatsapp:' . $to,
            [
                'from' => $this->from,
                'body' => $message,
            ]
        );
    }
}

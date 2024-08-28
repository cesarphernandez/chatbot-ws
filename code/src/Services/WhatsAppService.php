<?php

declare(strict_types=1);

namespace App\Services;

class WhatsAppService
{
    public function handleIncomingMessages(array $messages): void
    {
        foreach ($messages as $message) {
            if ($message['type'] === 'text') {
                $this->sendMessage($message['from'], 'Gracias por tu mensaje!');
            }
        }
    }

    public function sendMessage(string $to, string $text): void
    {
        //Send message to respond to the user
    }
}

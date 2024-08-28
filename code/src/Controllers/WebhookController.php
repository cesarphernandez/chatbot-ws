<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\Request;
use App\Services\WhatsAppService;
use App\Http\JsonResponse;

class WebhookController
{
    protected WhatsAppService $whatsAppService;

    public function __construct()
    {
        $this->whatsAppService = new WhatsAppService();
    }

    public function handleWhatsAppWebhook(Request $request): string
    {
        $input = file_get_contents('php://input');
        $payload = json_decode($input, true);

        $hubChallenge = $request->getQueryParam('hub_challenge');

        // Aquí puedes manejar diferentes tipos de eventos según lo que envíe WhatsApp
        if (isset($payload['messages'])) {
            $this->whatsAppService->handleIncomingMessages($payload['messages']);
        }

        return $hubChallenge;
    }
}

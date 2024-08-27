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
        $payload = $request->getBody();

        // Aquí puedes manejar diferentes tipos de eventos según lo que envíe WhatsApp
        if (isset($payload['messages'])) {
            $this->whatsAppService->handleIncomingMessages($payload['messages']);
        }

        return JsonResponse::send(['status' => 'received'], 200);
    }
}

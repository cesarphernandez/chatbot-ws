<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Http\JsonResponse;
use App\Http\Request;

class VerifyWebhookSignature implements Middleware
{
    public function handle(Request $request, callable $next): string
    {
        $signature = $request->getHeader('X-Hub-Signature');
        if (!$signature) {
            return JsonResponse::error('Missing signature', 400);
        }
        $payload = file_get_contents('php://input');
        $secret = 'test-de-prueba';

        if (!$this->isValidSignature($signature, $payload, $secret)) {
            return JsonResponse::error('Invalid signature', 403);
        }

        return $next($request);
    }

    private function isValidSignature(string $signature, string $payload, string $secret): bool
    {
        $expectedHash = 'sha1=' . hash_hmac('sha1', $payload, $secret);
        return hash_equals($expectedHash, $signature);
    }
}

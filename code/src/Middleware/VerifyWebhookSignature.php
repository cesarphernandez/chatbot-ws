<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Http\JsonResponse;
use App\Http\Request;

class VerifyWebhookSignature implements Middleware
{
    public function handle(Request $request, callable $next): string
    {
        $verifyToken= $request->getQueryParam('hub_verify_token');
        if (!isset($verifyToken)) {
            return JsonResponse::error('Invalid request', 400);
        }

        if (!$this->isValidToken($verifyToken)) {
            return JsonResponse::error('Invalid token', 403);
        }

        return $next($request);
    }

    private function isValidToken(string $verifyToken): bool
    {
        $token = 'test-de-prueba';
        return $verifyToken === $token;
    }
}

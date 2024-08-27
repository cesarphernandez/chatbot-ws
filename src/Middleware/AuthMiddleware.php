<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Http\Request;
use App\Http\JsonResponse;

class AuthMiddleware implements Middleware
{
    public function handle(Request $request, callable $next): string
    {
        $authenticated = $this->checkAuthentication($request);

        if (!$authenticated) {
            return JsonResponse::error('Unauthorized', 401);
        }

        return $next($request);
    }

    private function checkAuthentication(Request $request): bool
    {
        return $request->getHeader('authorization') === 'Bearer valid-token';
    }
}

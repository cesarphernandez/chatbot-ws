<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Http\Request;

interface Middleware
{
    public function handle(Request $request, callable $next): string;
}

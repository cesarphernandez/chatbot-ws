<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\Request;
use App\Http\JsonResponse;

class UserController
{
    public function index(Request $request): string
    {
        $limit = (int)($request->getQueryParam('limit') ?? 10);
        $users = [
            ['id' => 1, 'name' => 'Alice'],
            ['id' => 2, 'name' => 'Bob']
        ];
        return JsonResponse::build(array_slice($users, 0, $limit));
    }

    public function show(Request $request, string $id): string
    {
        $user = ['id' => (int)$id, 'name' => 'User ' . $id];
        return JsonResponse::build($user);
    }

    public function create(Request $request): string
    {
        $body = $request->getBody();
        $name = $body['name'] ?? '';
        $newUser = ['id' => 3, 'name' => $name];
        return JsonResponse::build($newUser, 201); // 201 Created
    }
}

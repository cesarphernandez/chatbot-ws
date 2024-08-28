<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Http\JsonResponse;
use App\Http\View;
use App\Http\Request;
use Exception;

class ViewController
{
    public function presentation(Request $request): string
    {
        try {
            return View::render('landing/presentation');
        } catch (Exception $e) {
            return self::notFound();
        }
    }


    public function user(Request $request): string
    {
        $id = 32;
        $user = [
            'id' => $id,
            'name' => 'User ' . $id,
            'email' => 'user' . $id . '@example.com'
        ];
        try {
            return View::render('landing/user', $user);
        } catch (Exception $e) {
            return self::notFound();
        }
    }

    public static function notFound(): string
    {
        try {
            return View::render('error/404');
        } catch (Exception $e) {
            return JsonResponse::error('Internal Server Error', 503);
        }
    }

}

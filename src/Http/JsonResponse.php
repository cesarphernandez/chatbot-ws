<?php

declare(strict_types=1);

namespace App\Http;

class JsonResponse
{
    /**
     * Build and return a JSON response
     *
     * @param mixed $data
     * @param int $statusCode
     * @return string
     */
    public static function build($data, int $statusCode = 200): string
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        return json_encode($data);
    }

    /**
     * Build and return a JSON error response
     *
     * @param string $message
     * @param int $statusCode
     * @return string
     */
    public static function error(string $message, int $statusCode = 500): string
    {
        return self::build([
            'error' => true,
            'message' => $message
        ], $statusCode);
    }
}

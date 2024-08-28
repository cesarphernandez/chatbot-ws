<?php

declare(strict_types=1);

namespace App\Http;

use Exception;

class View
{
    /**
     * Render a view
     *
     * @param string $template
     * @param array $data
     * @param int $statusCode
     * @return string
     * @throws Exception
     */
    public static function render(string $template, array $data = [], int $statusCode = 200): string
    {
        http_response_code($statusCode);
        header('Content-Type: text/html');

        $templatePath = __DIR__ . '/../../views/' . $template . '.php';

        if (!file_exists($templatePath)) {
            throw new Exception("View {$template} not found");
        }

        extract($data);
        ob_start();
        include $templatePath;

        return ob_get_clean();
    }
}

<?php

declare(strict_types=1);

namespace App\Http;

class Request
{
    private string $method;
    private string $uri;
    private array $headers;
    private array $queryParams;
    private ?array $body;

    public function __construct()
    {
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->uri = $_SERVER['REQUEST_URI'];
        $this->headers = getallheaders();
        $this->queryParams = $_GET;
        $this->body = $this->parseBody();
    }

    private function parseBody(): ?array
    {
        if ($this->method === 'GET') {
            return null;
        }
        if (isset($this->headers['Content-Type']) && $this->headers['Content-Type'] === 'application/json') {
            $content = file_get_contents('php://input');
            return json_decode($content, true) ?? [];
        }
        return $_POST;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getHeader(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }

    public function getQueryParams(): array
    {
        return $this->queryParams;
    }

    public function getQueryParam(string $name): ?string
    {
        return $this->queryParams[$name] ?? null;
    }

    public function getBody(): ?array
    {
        return $this->body;
    }

    public function getBodyParam(string $name): ?string
    {
        return $this->body[$name] ?? null;
    }
}
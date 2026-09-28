<?php

declare(strict_types=1);

namespace App\Http;

final class Request
{
    /**
     * @param array<string, mixed> $query
     * @param array<string, mixed> $post
     * @param array<string, mixed> $server
     */
    public function __construct(
        public readonly string $method,
        public readonly string $path,
        private readonly array $query = [],
        private readonly array $post = [],
        private readonly array $server = [],
        private readonly string $body = '',
    ) {
    }

    public static function fromGlobals(string $basePath = ''): self
    {
        $path = (string) parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);

        if ($basePath !== '' && str_starts_with($path, $basePath)) {
            $path = substr($path, strlen($basePath));
        }

        $path = '/' . trim($path, '/');

        return new self(
            strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET')),
            $path,
            $_GET,
            $_POST,
            $_SERVER,
            (string) file_get_contents('php://input'),
        );
    }

    public function query(string $key): ?string
    {
        $value = $this->query[$key] ?? null;

        return is_string($value) ? $value : null;
    }

    public function input(string $key): ?string
    {
        $value = $this->post[$key] ?? null;

        return is_string($value) ? $value : null;
    }

    /**
     * Returns only the string values of the given POST fields. Arrays sent
     * where a scalar is expected are dropped instead of reaching the app.
     *
     * @param list<string> $keys
     * @return array<string, string>
     */
    public function only(array $keys): array
    {
        $data = [];
        foreach ($keys as $key) {
            $data[$key] = $this->input($key) ?? '';
        }

        return $data;
    }

    /**
     * @return array<int|string, string>
     */
    public function inputMap(string $key): array
    {
        $value = $this->post[$key] ?? [];
        if (!is_array($value)) {
            return [];
        }

        return array_filter($value, static fn ($item, $index): bool => is_string($item) && is_scalar($index), ARRAY_FILTER_USE_BOTH);
    }

    public function body(): string
    {
        return $this->body;
    }

    public function isPost(): bool
    {
        return $this->method === 'POST';
    }

    /**
     * REMOTE_ADDR only. Forwarded headers are client-controlled unless a trusted
     * proxy rewrites them, so they are deliberately ignored here.
     */
    public function ip(): string
    {
        return (string) ($this->server['REMOTE_ADDR'] ?? '0.0.0.0');
    }

    public function fullPath(): string
    {
        $query = http_build_query($this->query);

        return $this->path . ($query === '' ? '' : '?' . $query);
    }
}

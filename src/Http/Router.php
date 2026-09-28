<?php

declare(strict_types=1);

namespace App\Http;

/**
 * Small method + path router. Route parameters are written as {name} and only
 * match positive integers, which is all this application needs (record ids).
 */
final class Router
{
    /** @var array<string, array<string, array{0: class-string, 1: string}>> */
    private array $routes = [];

    /**
     * @param array{0: class-string, 1: string} $handler
     */
    public function get(string $pattern, array $handler): void
    {
        $this->routes['GET'][$pattern] = $handler;
    }

    /**
     * @param array{0: class-string, 1: string} $handler
     */
    public function post(string $pattern, array $handler): void
    {
        $this->routes['POST'][$pattern] = $handler;
    }

    /**
     * @return array{handler: array{0: class-string, 1: string}, params: list<int>}
     */
    public function match(string $method, string $path): array
    {
        $allowed = false;

        foreach ($this->routes as $routeMethod => $routes) {
            foreach ($routes as $pattern => $handler) {
                $params = $this->matchPattern($pattern, $path);
                if ($params === null) {
                    continue;
                }

                if ($routeMethod === $method || ($method === 'HEAD' && $routeMethod === 'GET')) {
                    return ['handler' => $handler, 'params' => $params];
                }

                $allowed = true;
            }
        }

        throw $allowed ? HttpException::methodNotAllowed() : HttpException::notFound();
    }

    /**
     * @return list<int>|null
     */
    private function matchPattern(string $pattern, string $path): ?array
    {
        $regex = '#^' . preg_replace('#\{[a-z_]+\}#', '([1-9][0-9]{0,9})', $pattern) . '$#';

        if (preg_match($regex, $path, $matches) !== 1) {
            return null;
        }

        return array_map('intval', array_slice($matches, 1));
    }
}

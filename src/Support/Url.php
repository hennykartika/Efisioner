<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Builds application URLs. The base path is configured once at boot so that
 * templates can call url()/asset() without passing config around.
 */
final class Url
{
    private static string $basePath = '';
    private static string $absoluteBase = '';

    public static function configure(Config $config): void
    {
        self::$basePath = $config->basePath();
        self::$absoluteBase = $config->url;
    }

    public static function to(string $path = '/'): string
    {
        return self::$basePath . '/' . ltrim($path, '/');
    }

    public static function absolute(string $path = '/'): string
    {
        return self::$absoluteBase . '/' . ltrim($path, '/');
    }

    public static function basePath(): string
    {
        return self::$basePath;
    }
}

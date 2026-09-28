<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Reads KEY=value pairs from a .env file. Variables that are already set in the
 * real process environment take precedence, so production can inject secrets
 * without keeping a file on disk.
 */
final class Env
{
    /** @var array<string, string> */
    private static array $values = [];

    public static function load(string $path): void
    {
        if (!is_file($path) || !is_readable($path)) {
            return;
        }

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '' || $line[0] === '#' || !str_contains($line, '=')) {
                continue;
            }

            [$name, $value] = explode('=', $line, 2);
            $name = trim($name);

            if (preg_match('/^[A-Z][A-Z0-9_]*$/', $name) === 1) {
                self::$values[$name] = self::parseValue(trim($value));
            }
        }
    }

    public static function get(string $name, ?string $default = null): ?string
    {
        $value = getenv($name);
        if ($value === false) {
            $value = self::$values[$name] ?? null;
        }

        return $value === null || $value === '' ? $default : $value;
    }

    public static function bool(string $name, bool $default = false): bool
    {
        $value = self::get($name);
        if ($value === null) {
            return $default;
        }

        return filter_var($value, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) ?? $default;
    }

    public static function int(string $name, int $default): int
    {
        $value = self::get($name);
        if ($value === null) {
            return $default;
        }

        $parsed = filter_var($value, FILTER_VALIDATE_INT);

        return $parsed === false ? $default : $parsed;
    }

    private static function parseValue(string $value): string
    {
        $quote = $value[0] ?? '';
        if (($quote === '"' || $quote === "'") && str_ends_with($value, $quote) && strlen($value) > 1) {
            return substr($value, 1, -1);
        }

        $commentAt = strpos($value, ' #');

        return $commentAt === false ? $value : rtrim(substr($value, 0, $commentAt));
    }
}

<?php

declare(strict_types=1);

use App\Support\Url;

function e(string|int|float|null $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML5, 'UTF-8');
}

function url(string $path = '/'): string
{
    return Url::to($path);
}

function asset(string $path): string
{
    $file = dirname(__DIR__) . '/public/assets/' . ltrim($path, '/');
    $version = is_file($file) ? '?v=' . filemtime($file) : '';

    return Url::to('assets/' . ltrim($path, '/')) . $version;
}

function rupiah(int $amount): string
{
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

function format_date(?string $value): string
{
    return $value === null ? '-' : date('j M Y', (int) strtotime($value));
}

function format_datetime(?string $value): string
{
    return $value === null ? '-' : date('j M Y, H:i', (int) strtotime($value));
}

/**
 * Encodes data for an HTML attribute that JavaScript will JSON.parse().
 */
function json_attr(mixed $data): string
{
    return e(json_encode($data, JSON_THROW_ON_ERROR | JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE));
}

/**
 * @param array<string, string|int|bool|null> $attributes true renders a bare attribute, false/null skips it
 */
function html_attributes(array $attributes): string
{
    $html = '';
    foreach ($attributes as $name => $value) {
        if ($value === false || $value === null) {
            continue;
        }

        $html .= ' ' . e($name) . ($value === true ? '' : '="' . e($value) . '"');
    }

    return $html;
}

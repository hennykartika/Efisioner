<?php

declare(strict_types=1);

namespace App\Security;

use App\Http\Response;
use App\Support\Config;

final class SecurityHeaders
{
    private const CDN = 'https://cdn.jsdelivr.net';

    public function __construct(private readonly Config $config)
    {
    }

    public function apply(Response $response): Response
    {
        $headers = [
            'Content-Security-Policy' => $this->contentSecurityPolicy(),
            'X-Content-Type-Options' => 'nosniff',
            'X-Frame-Options' => 'DENY',
            'Referrer-Policy' => 'strict-origin-when-cross-origin',
            'Permissions-Policy' => 'camera=(), microphone=(), geolocation=()',
            'Cross-Origin-Opener-Policy' => 'same-origin',
        ];

        if ($this->config->usesHttps()) {
            $headers['Strict-Transport-Security'] = 'max-age=31536000; includeSubDomains';
        }

        if ($response->header('Cache-Control') === null) {
            $headers['Cache-Control'] = 'no-store';
        }

        foreach ($headers as $name => $value) {
            $response = $response->withHeader($name, $value);
        }

        return $response;
    }

    private function contentSecurityPolicy(): string
    {
        // Checkout posts to our server, which redirects to the Midtrans page.
        // Browsers apply form-action to that redirect as well.
        $midtrans = $this->config->midtransProduction
            ? 'https://app.midtrans.com'
            : 'https://app.sandbox.midtrans.com';

        return implode('; ', [
            "default-src 'self'",
            "script-src 'self' " . self::CDN,
            "style-src 'self' " . self::CDN,
            "img-src 'self' data:",
            "font-src 'self' " . self::CDN,
            "connect-src 'self'",
            "object-src 'none'",
            "base-uri 'self'",
            "frame-ancestors 'none'",
            "form-action 'self' " . $midtrans,
        ]);
    }
}

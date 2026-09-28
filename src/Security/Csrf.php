<?php

declare(strict_types=1);

namespace App\Security;

use App\Http\Session;

final class Csrf
{
    public const FIELD = '_token';
    private const SESSION_KEY = '_csrf_token';

    public function __construct(private readonly Session $session)
    {
    }

    public function token(): string
    {
        $token = $this->session->get(self::SESSION_KEY);

        if (!is_string($token) || strlen($token) !== 64) {
            $token = bin2hex(random_bytes(32));
            $this->session->set(self::SESSION_KEY, $token);
        }

        return $token;
    }

    public function isValid(?string $token): bool
    {
        $expected = $this->session->get(self::SESSION_KEY);

        return is_string($expected) && is_string($token) && hash_equals($expected, $token);
    }

    public function rotate(): void
    {
        $this->session->forget(self::SESSION_KEY);
    }
}

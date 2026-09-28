<?php

declare(strict_types=1);

namespace App\Http;

use App\Support\Config;

final class Session
{
    private const IDLE_TIMEOUT_SECONDS = 7200;

    public function start(Config $config): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        ini_set('session.use_strict_mode', '1');
        ini_set('session.use_only_cookies', '1');
        ini_set('session.use_trans_sid', '0');

        session_name('efisioner_session');
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => $config->basePath() === '' ? '/' : $config->basePath() . '/',
            'secure' => $config->usesHttps(),
            'httponly' => true,
            'samesite' => 'Lax',
        ]);

        session_start();

        $lastSeen = $_SESSION['_last_seen'] ?? null;
        if (is_int($lastSeen) && time() - $lastSeen > self::IDLE_TIMEOUT_SECONDS) {
            $this->invalidate();
        }

        $_SESSION['_last_seen'] = time();
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function forget(string $key): void
    {
        unset($_SESSION[$key]);
    }

    public function pull(string $key, mixed $default = null): mixed
    {
        $value = $this->get($key, $default);
        $this->forget($key);

        return $value;
    }

    public function flash(string $type, string $message): void
    {
        $_SESSION['_flash'][] = ['type' => $type, 'message' => $message];
    }

    /**
     * @return list<array{type: string, message: string}>
     */
    public function takeFlashes(): array
    {
        $messages = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);

        return is_array($messages) ? array_values($messages) : [];
    }

    /**
     * New session id, same data. Call after any privilege change (login).
     */
    public function regenerate(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }

    public function invalidate(): void
    {
        $_SESSION = [];

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }
    }
}

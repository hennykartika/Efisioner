<?php

declare(strict_types=1);

namespace App\Support;

use RuntimeException;

final class Config
{
    public function __construct(
        public readonly string $env,
        public readonly bool $debug,
        public readonly string $url,
        public readonly string $timezone,
        public readonly string $dbHost,
        public readonly int $dbPort,
        public readonly string $dbName,
        public readonly string $dbUser,
        public readonly string $dbPassword,
        public readonly string $midtransServerKey,
        public readonly bool $midtransProduction,
        public readonly int $platformFee,
    ) {
    }

    public static function fromEnvironment(): self
    {
        $config = new self(
            env: Env::get('APP_ENV', 'production'),
            debug: Env::bool('APP_DEBUG'),
            url: rtrim(self::required('APP_URL'), '/'),
            timezone: Env::get('APP_TIMEZONE', 'Asia/Jakarta'),
            dbHost: Env::get('DB_HOST', '127.0.0.1'),
            dbPort: Env::int('DB_PORT', 3306),
            dbName: self::required('DB_DATABASE'),
            dbUser: self::required('DB_USERNAME'),
            dbPassword: Env::get('DB_PASSWORD', ''),
            midtransServerKey: Env::get('MIDTRANS_SERVER_KEY', ''),
            midtransProduction: Env::bool('MIDTRANS_IS_PRODUCTION'),
            platformFee: Env::int('PLATFORM_FEE', 1000),
        );

        if ($config->isProduction() && $config->debug) {
            throw new RuntimeException('APP_DEBUG must be disabled when APP_ENV is production.');
        }

        if (filter_var($config->url, FILTER_VALIDATE_URL) === false) {
            throw new RuntimeException('APP_URL must be an absolute URL.');
        }

        return $config;
    }

    public function isProduction(): bool
    {
        return $this->env === 'production';
    }

    public function usesHttps(): bool
    {
        return str_starts_with($this->url, 'https://');
    }

    /**
     * Path prefix when the app is served from a sub-directory, e.g. "/efisioner".
     */
    public function basePath(): string
    {
        return rtrim((string) parse_url($this->url, PHP_URL_PATH), '/');
    }

    private static function required(string $name): string
    {
        $value = Env::get($name);
        if ($value === null) {
            throw new RuntimeException(sprintf('Missing required environment variable %s.', $name));
        }

        return $value;
    }
}

<?php

declare(strict_types=1);

namespace App\Http;

use RuntimeException;

final class HttpException extends RuntimeException
{
    public function __construct(
        public readonly int $status,
        string $message = '',
        public readonly ?string $title = null,
    ) {
        parent::__construct($message, $status);
    }

    public static function notFound(): self
    {
        return new self(404, 'The page you are looking for does not exist.', 'Page not found');
    }

    public static function forbidden(): self
    {
        return new self(403, 'You do not have permission to access this page.', 'Access denied');
    }

    public static function methodNotAllowed(): self
    {
        return new self(405, 'Method not allowed.', 'Method not allowed');
    }

    /**
     * 403 rather than the non-standard 419: Apache turns unknown codes into 500.
     */
    public static function csrfMismatch(): self
    {
        return new self(403, 'Your session has expired. Please go back, refresh the page and try again.', 'Session expired');
    }
}

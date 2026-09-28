<?php

declare(strict_types=1);

namespace App\Http;

use RuntimeException;

/**
 * Thrown when a page needs a signed-in user. The kernel turns it into a
 * redirect to the login form and remembers where the user was heading.
 */
final class AuthenticationRequired extends RuntimeException
{
}

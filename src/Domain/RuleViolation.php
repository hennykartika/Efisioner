<?php

declare(strict_types=1);

namespace App\Domain;

use DomainException;

/**
 * A business rule prevented the action. The message is safe to show to users.
 */
final class RuleViolation extends DomainException
{
}

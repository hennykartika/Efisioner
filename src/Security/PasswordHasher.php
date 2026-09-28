<?php

declare(strict_types=1);

namespace App\Security;

final class PasswordHasher
{
    public const MIN_LENGTH = 8;

    /** bcrypt silently ignores anything past 72 bytes, so longer input is rejected. */
    public const MAX_BYTES = 72;

    private const OPTIONS = ['cost' => 12];

    /**
     * Hash of a random throwaway value with the same cost as real hashes.
     * Verifying against it when a username does not exist keeps the response
     * time the same, so attackers cannot probe which usernames are registered.
     */
    private const DUMMY_HASH = '$2y$12$QP14w/7LrGg80RKyavZyxO3kWbESqsCZJlv5mPaXVLXp3MbPOJ9H2';

    public function hash(string $password): string
    {
        return password_hash($password, PASSWORD_BCRYPT, self::OPTIONS);
    }

    public function verify(string $password, ?string $hash): bool
    {
        $matches = password_verify($password, $hash ?? self::DUMMY_HASH);

        return $hash !== null && $matches;
    }

    public function needsRehash(string $hash): bool
    {
        return password_needs_rehash($hash, PASSWORD_BCRYPT, self::OPTIONS);
    }

    /**
     * Passwords are checked raw: no trimming, every character counts.
     */
    public static function policyError(string $password): ?string
    {
        if (mb_strlen($password) < self::MIN_LENGTH) {
            return sprintf('Password must be at least %d characters.', self::MIN_LENGTH);
        }

        if (strlen($password) > self::MAX_BYTES) {
            return sprintf('Password must be at most %d characters.', self::MAX_BYTES);
        }

        return null;
    }
}

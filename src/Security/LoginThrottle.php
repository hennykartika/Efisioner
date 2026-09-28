<?php

declare(strict_types=1);

namespace App\Security;

use PDO;

/**
 * Limits failed sign-in attempts per (username, IP) pair and per IP.
 * Usernames are stored hashed so typos that are really passwords never end
 * up in the database in clear text.
 */
final class LoginThrottle
{
    private const WINDOW_MINUTES = 15;
    private const MAX_PER_ACCOUNT_AND_IP = 5;
    private const MAX_PER_IP = 20;

    public function __construct(private readonly PDO $db)
    {
    }

    public function tooManyAttempts(string $username, string $ip): bool
    {
        $statement = $this->db->prepare(
            'SELECT
                SUM(username_hash = :hash) AS for_account,
                COUNT(*) AS for_ip
             FROM login_attempts
             WHERE ip_address = :ip
               AND attempted_at > NOW() - INTERVAL ' . self::WINDOW_MINUTES . ' MINUTE',
        );
        $statement->execute(['hash' => $this->hash($username), 'ip' => $ip]);
        $row = $statement->fetch();

        return (int) $row['for_account'] >= self::MAX_PER_ACCOUNT_AND_IP
            || (int) $row['for_ip'] >= self::MAX_PER_IP;
    }

    public function recordFailure(string $username, string $ip): void
    {
        $this->db->prepare('INSERT INTO login_attempts (ip_address, username_hash) VALUES (?, ?)')
            ->execute([$ip, $this->hash($username)]);

        $this->db->exec('DELETE FROM login_attempts WHERE attempted_at < NOW() - INTERVAL 1 DAY');
    }

    public function clear(string $username, string $ip): void
    {
        $this->db->prepare('DELETE FROM login_attempts WHERE ip_address = ? AND username_hash = ?')
            ->execute([$ip, $this->hash($username)]);
    }

    public static function windowMinutes(): int
    {
        return self::WINDOW_MINUTES;
    }

    private function hash(string $username): string
    {
        return hash('sha256', mb_strtolower($username));
    }
}

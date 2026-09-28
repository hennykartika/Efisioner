<?php

declare(strict_types=1);

namespace App\Support;

use DateTimeImmutable;
use PDO;

final class Database
{
    public static function connect(Config $config): PDO
    {
        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            $config->dbHost,
            $config->dbPort,
            $config->dbName,
        );

        $pdo = new PDO($dsn, $config->dbUser, $config->dbPassword, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false,
        ]);

        // Keep NOW() in SQL consistent with PHP's configured timezone.
        $offset = (new DateTimeImmutable())->format('P');
        $pdo->prepare('SET time_zone = ?')->execute([$offset]);

        return $pdo;
    }

    /**
     * Runs $callback inside a transaction and rolls back on any exception.
     *
     * @template T
     * @param callable(): T $callback
     * @return T
     */
    public static function transaction(PDO $pdo, callable $callback): mixed
    {
        $pdo->beginTransaction();

        try {
            $result = $callback();
            $pdo->commit();

            return $result;
        } catch (\Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }

            throw $e;
        }
    }

    public static function isDuplicateKey(\PDOException $e): bool
    {
        return ($e->errorInfo[1] ?? null) === 1062;
    }
}

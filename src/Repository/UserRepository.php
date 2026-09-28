<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\Role;
use App\Domain\User;
use PDO;

final class UserRepository
{
    private const PROFILE_COLUMNS = [
        'full_name', 'email', 'phone', 'address', 'birth_date', 'gender',
        'education', 'occupation', 'occupation_other', 'institution',
    ];

    public function __construct(private readonly PDO $db)
    {
    }

    public function find(int $id): ?User
    {
        $statement = $this->db->prepare('SELECT * FROM users WHERE id = ?');
        $statement->execute([$id]);
        $row = $statement->fetch();

        return $row === false ? null : User::fromRow($row);
    }

    /**
     * @return array{id: int, password_hash: string}|null
     */
    public function findCredentials(string $username): ?array
    {
        $statement = $this->db->prepare('SELECT id, password_hash FROM users WHERE username = ?');
        $statement->execute([$username]);
        $row = $statement->fetch();

        return $row === false ? null : ['id' => (int) $row['id'], 'password_hash' => (string) $row['password_hash']];
    }

    public function passwordHash(int $id): ?string
    {
        $statement = $this->db->prepare('SELECT password_hash FROM users WHERE id = ?');
        $statement->execute([$id]);
        $hash = $statement->fetchColumn();

        return $hash === false ? null : (string) $hash;
    }

    public function usernameExists(string $username): bool
    {
        $statement = $this->db->prepare('SELECT 1 FROM users WHERE username = ?');
        $statement->execute([$username]);

        return $statement->fetchColumn() !== false;
    }

    public function emailExists(string $email, ?int $exceptUserId = null): bool
    {
        $statement = $this->db->prepare('SELECT 1 FROM users WHERE email = ? AND id <> ?');
        $statement->execute([$email, $exceptUserId ?? 0]);

        return $statement->fetchColumn() !== false;
    }

    /**
     * @param array<string, string|null> $profile keys from PROFILE_COLUMNS
     */
    public function create(string $username, string $passwordHash, array $profile): int
    {
        $columns = ['username', 'password_hash', ...self::PROFILE_COLUMNS];
        $values = [$username, $passwordHash, ...$this->profileValues($profile)];

        $statement = $this->db->prepare(sprintf(
            'INSERT INTO users (%s) VALUES (%s)',
            implode(', ', $columns),
            implode(', ', array_fill(0, count($columns), '?')),
        ));
        $statement->execute($values);

        return (int) $this->db->lastInsertId();
    }

    /**
     * @param array<string, string|null> $profile keys from PROFILE_COLUMNS
     */
    public function updateProfile(int $id, array $profile): void
    {
        $assignments = implode(', ', array_map(static fn (string $column): string => $column . ' = ?', self::PROFILE_COLUMNS));

        $statement = $this->db->prepare("UPDATE users SET {$assignments} WHERE id = ?");
        $statement->execute([...$this->profileValues($profile), $id]);
    }

    public function updatePasswordHash(int $id, string $hash): void
    {
        $this->db->prepare('UPDATE users SET password_hash = ? WHERE id = ?')->execute([$hash, $id]);
    }

    public function setRole(string $username, Role $role): bool
    {
        $statement = $this->db->prepare('UPDATE users SET role = ? WHERE username = ?');
        $statement->execute([$role->value, $username]);

        return $statement->rowCount() > 0 || $this->usernameExists($username);
    }

    /**
     * @param array<string, string|null> $profile
     * @return list<string|null>
     */
    private function profileValues(array $profile): array
    {
        return array_map(static fn (string $column): ?string => $profile[$column] ?? null, self::PROFILE_COLUMNS);
    }
}

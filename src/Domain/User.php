<?php

declare(strict_types=1);

namespace App\Domain;

use DateTimeImmutable;

final class User
{
    public function __construct(
        public readonly int $id,
        public readonly string $username,
        public readonly string $email,
        public readonly Role $role,
        public readonly string $fullName,
        public readonly ?string $phone,
        public readonly ?string $address,
        public readonly DateTimeImmutable $birthDate,
        public readonly Gender $gender,
        public readonly Education $education,
        public readonly Occupation $occupation,
        public readonly ?string $occupationOther,
        public readonly ?string $institution,
        public readonly int $rewardBalance,
    ) {
    }

    /**
     * @param array<string, mixed> $row
     */
    public static function fromRow(array $row): self
    {
        return new self(
            id: (int) $row['id'],
            username: (string) $row['username'],
            email: (string) $row['email'],
            role: Role::from((string) $row['role']),
            fullName: (string) $row['full_name'],
            phone: $row['phone'] ?? null,
            address: $row['address'] ?? null,
            birthDate: new DateTimeImmutable((string) $row['birth_date']),
            gender: Gender::from((string) $row['gender']),
            education: Education::from((string) $row['education']),
            occupation: Occupation::from((string) $row['occupation']),
            occupationOther: $row['occupation_other'] ?? null,
            institution: $row['institution'] ?? null,
            rewardBalance: (int) $row['reward_balance'],
        );
    }

    public function isAdmin(): bool
    {
        return $this->role === Role::Admin;
    }

    public function ageOn(DateTimeImmutable $date): int
    {
        return $this->birthDate->diff($date)->y;
    }

    public function occupationLabel(): string
    {
        return Occupation::describe($this->occupation->value, $this->occupationOther);
    }

    public function initials(): string
    {
        $parts = preg_split('/\s+/', trim($this->fullName)) ?: [];
        $letters = array_map(static fn (string $part): string => mb_substr($part, 0, 1), array_slice($parts, 0, 2));

        return mb_strtoupper(implode('', $letters));
    }
}

<?php

declare(strict_types=1);

namespace App\Security;

use App\Domain\User;
use App\Http\Session;
use App\Repository\UserRepository;

/**
 * Only the user id lives in the session. The record is reloaded on every
 * request, so a role change or deleted account takes effect immediately.
 */
final class Auth
{
    private const SESSION_KEY = 'user_id';

    private ?User $user = null;
    private bool $resolved = false;

    public function __construct(
        private readonly Session $session,
        private readonly Csrf $csrf,
        private readonly UserRepository $users,
        private readonly PasswordHasher $hasher,
    ) {
    }

    public function user(): ?User
    {
        if (!$this->resolved) {
            $this->resolved = true;
            $id = $this->session->get(self::SESSION_KEY);
            $this->user = is_int($id) ? $this->users->find($id) : null;

            if ($id !== null && $this->user === null) {
                $this->session->invalidate();
            }
        }

        return $this->user;
    }

    public function attempt(string $username, string $password): ?User
    {
        $credentials = $this->users->findCredentials($username);

        if (!$this->hasher->verify($password, $credentials['password_hash'] ?? null) || $credentials === null) {
            return null;
        }

        if ($this->hasher->needsRehash($credentials['password_hash'])) {
            $this->users->updatePasswordHash($credentials['id'], $this->hasher->hash($password));
        }

        return $this->users->find($credentials['id']);
    }

    public function login(User $user): void
    {
        $this->session->regenerate();
        $this->csrf->rotate();
        $this->session->set(self::SESSION_KEY, $user->id);
        $this->user = $user;
        $this->resolved = true;
    }

    public function logout(): void
    {
        $this->session->invalidate();
        $this->user = null;
        $this->resolved = true;
    }
}

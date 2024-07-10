<?php

namespace App\Security;

use Symfony\Component\Security\Core\User\UserInterface;

class ApiKeyUser implements UserInterface
{
    public function __construct(private string $username)
    {
    }

    public function getRoles(): array
    {
        return ['ROLE_API_USER'];
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }
}

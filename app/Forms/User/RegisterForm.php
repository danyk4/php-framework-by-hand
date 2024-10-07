<?php

namespace App\Forms\User;

use App\Entites\User;
use App\Services\UserService;

class RegisterForm
{
    private ?string $name;
    private string $email;
    private string $password;
    private string $passwordConfirmation;

    public function __construct(
        private UserService $userService,
    ) {
    }

    public function setFields(string $email, string $password, string $passwordConfirmation, ?string $name = null): void
    {
        $this->email                = $email;
        $this->password             = $password;
        $this->passwordConfirmation = $passwordConfirmation;
        $this->name                 = $name;
    }

    public function save(): User
    {
        $user = User::create(
            $this->email,
            password_hash($this->password, PASSWORD_DEFAULT),
            new \DateTimeImmutable(),
            $this->name
        );

        $user = $this->userService->save($user);

        return $user;
    }

    public function hasValidationErrors(): bool
    {
        return ! empty($this->getValidationErrors());
    }

    public function getValidationErrors(): array
    {
        $errors = [];

        if ( ! empty($this->name) && strlen($this->name) > 50) {
            $errors[] = 'Max name length is 50 characters';
        }

        if (empty($this->email) || ! filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Invalid email address';
        }

        if (empty($this->password) || strlen($this->password) < 8) {
            $errors[] = 'Password must be at least 8 characters';
        }

        if ($this->password !== $this->passwordConfirmation) {
            $errors[] = 'Passwords do not match';
        }

        return $errors;
    }
}

<?php

namespace danyk\Framework\Authentication;

interface UserServiceInterface
{
    public function findByEmail(string $email): ?AuthUserInterface;
}

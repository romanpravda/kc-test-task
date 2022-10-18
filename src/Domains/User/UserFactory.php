<?php

declare(strict_types=1);

namespace Romanpravda\KcTestTask\Domains\User;

use Romanpravda\KcTestTask\Support\PasswordHasher\PasswordHasherInterface;

final class UserFactory
{
    /**
     * Password hasher.
     *
     * @var \Romanpravda\KcTestTask\Support\PasswordHasher\PasswordHasherInterface
     */
    private PasswordHasherInterface $passwordHasher;

    /**
     * UserFactory constructor.
     *
     * @param \Romanpravda\KcTestTask\Support\PasswordHasher\PasswordHasherInterface $passwordHasher
     */
    public function __construct(PasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    /**
     * Creating user domain model.
     *
     * @param int|null $id
     * @param string $email
     * @param string $username
     * @param string $password
     *
     * @return \Romanpravda\KcTestTask\Domains\User\User
     */
    public function create(?int $id, string $email, string $username, string $password): User
    {
        return new User($id, $email, $username, $password, $this->passwordHasher);
    }
}

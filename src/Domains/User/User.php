<?php

declare(strict_types=1);

namespace Romanpravda\KcTestTask\Domains\User;

use Romanpravda\KcTestTask\Support\PasswordHasher\PasswordHasher;
use Romanpravda\KcTestTask\Support\PasswordHasher\PasswordHasherInterface;

final class User
{
    /**
     * User's ID.
     *
     * @var int|null
     */
    private ?int $id;

    /**
     * User's email.
     *
     * @var string
     */
    private string $email;

    /**
     * User's name.
     *
     * @var string
     */
    private string $username;

    /**
     * User's password.
     *
     * @var string
     */
    private string $password;

    /**
     * Password hasher.
     *
     * @var \Romanpravda\KcTestTask\Support\PasswordHasher\PasswordHasherInterface
     */
    private PasswordHasherInterface $passwordHasher;

    /**
     * User constructor.
     *
     * @param int|null $id
     * @param string $email
     * @param string $username
     * @param string $password
     *
     * @param \Romanpravda\KcTestTask\Support\PasswordHasher\PasswordHasherInterface|null $passwordHasher
     */
    public function __construct(?int $id, string $email, string $username, string $password, ?PasswordHasherInterface $passwordHasher = null)
    {
        $this->id = $id;
        $this->setEmail($email);
        $this->setUsername($username);
        $this->password = $password;

        $this->passwordHasher = $passwordHasher ?? new PasswordHasher();
    }

    /**
     * Retrieving user's ID.
     *
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Setting user's ID.
     *
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Retrieving user's email.
     *
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Setting user's email.
     *
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * Retrieving user's username.
     *
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * Setting user's username.
     *
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    /**
     * Retrieving user's password.
     *
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * Setting user's password.
     *
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $this->passwordHasher->makeHash($password);
    }

    /**
     * Checking if given password matches user's password.
     *
     * @param string $passwordForCheck
     *
     * @return bool
     */
    public function checkPassword(string $passwordForCheck): bool
    {
        return $this->passwordHasher->makeHash($passwordForCheck) === $this->getPassword();
    }
}
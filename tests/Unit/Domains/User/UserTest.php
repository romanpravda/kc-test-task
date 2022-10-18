<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\User;

use Faker\Factory;
use PHPUnit\Framework\TestCase;
use Romanpravda\KcTestTask\Domains\User\User;
use Romanpravda\KcTestTask\Support\PasswordHasher\PasswordHasher;

final class UserTest extends TestCase
{
    public function test_user(): void
    {
        $faker = Factory::create(Factory::DEFAULT_LOCALE);

        $passwordHasher = new PasswordHasher();

        $email = $faker->email();
        $username = sprintf('%s %s', $faker->name(), $faker->lastName());
        $password = $faker->password();
        $hashedPassword = $passwordHasher->makeHash($password);

        $user = new User(null, $email, $username, $hashedPassword, $passwordHasher);

        $this->assertNull(
            $user->getId(),
        );
        $this->assertEquals(
            $email,
            $user->getEmail(),
        );
        $this->assertEquals(
            $username,
            $user->getUsername(),
        );
        $this->assertEquals(
            $hashedPassword,
            $user->getPassword(),
        );
        $this->assertTrue(
            $user->checkPassword($password),
        );

        $id = 1;
        $newPassword = $faker->word();
        $newHashedPassword = $passwordHasher->makeHash($newPassword);

        $user->setId($id);
        $user->setPassword($newPassword);

        $this->assertEquals(
            $id,
            $user->getId(),
        );
        $this->assertEquals(
            $newHashedPassword,
            $user->getPassword(),
        );
        $this->assertFalse(
            $user->checkPassword($password),
        );
        $this->assertTrue(
            $user->checkPassword($newPassword),
        );
    }
}
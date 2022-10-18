<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\User;

use Faker\Factory;
use PHPUnit\Framework\TestCase;
use Romanpravda\KcTestTask\Domains\User\UserFactory;
use Romanpravda\KcTestTask\Support\PasswordHasher\PasswordHasher;

final class UserFactoryTest extends TestCase
{
    public function test_user_factory(): void
    {
        $faker = Factory::create(Factory::DEFAULT_LOCALE);

        $passwordHasher = new PasswordHasher();

        $email = $faker->email();
        $username = sprintf('%s %s', $faker->name(), $faker->lastName());
        $password = $faker->password();

        $userFactory = new UserFactory($passwordHasher);
        $user = $userFactory->create(null, $email, $username, $password);

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
            $password,
            $user->getPassword(),
        );
    }
}
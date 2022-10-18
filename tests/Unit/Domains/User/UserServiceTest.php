<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\User;

use DateTimeImmutable;
use Faker\Factory;
use Lcobucci\Clock\FrozenClock;
use Lcobucci\JWT\JwtFacade;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\RegisteredClaims;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;
use PHPUnit\Framework\TestCase;
use Romanpravda\KcTestTask\Domains\Token\JWTTokenService;
use Romanpravda\KcTestTask\Domains\User\User;
use Romanpravda\KcTestTask\Domains\User\UserService;
use Romanpravda\KcTestTask\Exceptions\Domains\User\WrongCredentialsException;
use Romanpravda\KcTestTask\Support\PasswordHasher\PasswordHasher;
use Tests\Mocks\MockedTokenRepository;
use Tests\Mocks\MockedUserRepository;
use Tests\Traits\GeneratesRandomString;

final class UserServiceTest extends TestCase
{
    use GeneratesRandomString;

    public function test_authenticate_user(): void
    {
        $faker = Factory::create(Factory::DEFAULT_LOCALE);

        $key = base64_encode($this->random_str());
        $signer = new Sha256();
        $tokenRepository = new MockedTokenRepository();
        $tokenService = new JWTTokenService($key, $signer, $tokenRepository);

        $passwordHasher = new PasswordHasher();
        $userRepository = new MockedUserRepository();

        $userService = new UserService($userRepository, $tokenService);

        $username = $faker->userName();
        $password = $faker->password(10);
        $hashedPassword = $passwordHasher->makeHash($password);

        $user = $userRepository->save(new User(null, $faker->email(), $username, $hashedPassword, $passwordHasher));
        $token = $userService->authenticateUser($username, $password);

        $clock = new FrozenClock((new DateTimeImmutable()));
        $signerKey = InMemory::base64Encoded($key);

        $parsedToken = (new JwtFacade())->parse($token,
            new SignedWith($signer, $signerKey),
            new StrictValidAt($clock),
        );

        $this->assertEquals(
            $user->getId(),
            (int) $parsedToken->claims()->get(RegisteredClaims::SUBJECT),
        );
    }

    public function test_authenticate_user_not_found(): void
    {
        $faker = Factory::create(Factory::DEFAULT_LOCALE);

        $key = base64_encode($this->random_str());
        $signer = new Sha256();
        $tokenRepository = new MockedTokenRepository();
        $tokenService = new JWTTokenService($key, $signer, $tokenRepository);

        $userRepository = new MockedUserRepository();

        $userService = new UserService($userRepository, $tokenService);

        $this->expectException(WrongCredentialsException::class);
        $this->expectExceptionMessage('Wrong username or password');

        $userService->authenticateUser($faker->userName(), $faker->password(10));
    }

    public function test_authenticate_user_wrong_password(): void
    {
        $faker = Factory::create(Factory::DEFAULT_LOCALE);

        $key = base64_encode($this->random_str());
        $signer = new Sha256();
        $tokenRepository = new MockedTokenRepository();
        $tokenService = new JWTTokenService($key, $signer, $tokenRepository);

        $passwordHasher = new PasswordHasher();
        $userRepository = new MockedUserRepository();

        $userService = new UserService($userRepository, $tokenService);

        $username = $faker->userName();
        $password = $faker->password(10);
        $hashedPassword = $passwordHasher->makeHash($password);

        $otherPassword = $faker->password(10);

        $userRepository->save(new User(null, $faker->email(), $username, $hashedPassword, $passwordHasher));

        $this->expectException(WrongCredentialsException::class);
        $this->expectExceptionMessage('Wrong username or password');

        $userService->authenticateUser($username, $otherPassword);
    }
}
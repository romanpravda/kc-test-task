<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\Token;

use DateTimeImmutable;
use Lcobucci\Clock\FrozenClock;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\JwtFacade;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\RegisteredClaims;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;
use PHPUnit\Framework\TestCase;
use Romanpravda\KcTestTask\Domains\Token\JWTTokenService;
use Romanpravda\KcTestTask\Domains\Token\Token;
use Romanpravda\KcTestTask\Exceptions\Domains\Token\TokenNotFound;
use Tests\Mocks\MockedTokenRepository;
use Tests\Traits\GeneratesRandomString;

final class JWTTokenServiceTest extends TestCase
{
    use GeneratesRandomString;

    public function test_issuing_token(): void
    {
        $key = base64_encode($this->generateRandomString());
        $signer = new Sha256();
        $repository = new MockedTokenRepository();

        $userId = 1;

        $tokenService = new JWTTokenService($key, $signer, $repository);
        $token = $tokenService->issueTokenForUserId($userId);

        $clock = new FrozenClock((new DateTimeImmutable()));
        $signerKey = InMemory::base64Encoded($key);

        $parsedToken = (new JwtFacade())->parse($token,
            new SignedWith($signer, $signerKey),
            new StrictValidAt($clock),
        );

        $this->assertEquals(
            $userId,
            (int) $parsedToken->claims()->get(RegisteredClaims::SUBJECT),
        );
    }

    public function test_issuing_token_check_expire(): void
    {
        $key = base64_encode($this->generateRandomString());
        $signer = new Sha256();
        $repository = new MockedTokenRepository();

        $userId = 1;

        $tokenService = new JWTTokenService($key, $signer, $repository);
        $token = $tokenService->issueTokenForUserId($userId);

        $clock = new FrozenClock((new DateTimeImmutable())->modify('+15 minutes'));
        $signerKey = InMemory::base64Encoded($key);

        $this->expectException(RequiredConstraintsViolated::class);
        $this->expectExceptionMessage('The token violates some mandatory constraints, details:
- The token is expired');

        (new JwtFacade())->parse($token,
            new SignedWith($signer, $signerKey),
            new StrictValidAt($clock),
        );
    }

    public function test_get_user_from_token(): void
    {
        $key = base64_encode($this->generateRandomString());
        $signer = new Sha256();
        $repository = new MockedTokenRepository();

        $tokenService = new JWTTokenService($key, $signer, $repository);

        $clock = new FrozenClock((new DateTimeImmutable()));
        $signerKey = InMemory::base64Encoded($key);

        $userId = 2;
        $token = new Token(null, $userId);
        $token = $repository->save($token);

        $tokenAsString = (new JwtFacade(null, $clock))->issue($signer, $signerKey,
            static fn (Builder $builder, DateTimeImmutable $issuedAt): Builder => $builder
                ->expiresAt($issuedAt->modify('+10 minutes'))
                ->relatedTo((string) $token->getUserId())
                ->identifiedBy((string) $token->getId()),
        )->toString();

        $userIdFromToken = $tokenService->getUserIdFromToken($tokenAsString);
        $this->assertEquals(
            $userId,
            $userIdFromToken,
        );
    }

    public function test_get_user_from_token_check_expire(): void
    {
        $key = base64_encode($this->generateRandomString());
        $signer = new Sha256();
        $repository = new MockedTokenRepository();

        $tokenService = new JWTTokenService($key, $signer, $repository);

        $clock = new FrozenClock((new DateTimeImmutable()));
        $signerKey = InMemory::base64Encoded($key);

        $userId = 2;
        $token = new Token(null, $userId);
        $token = $repository->save($token);

        $tokenAsString = (new JwtFacade(null, $clock))->issue($signer, $signerKey,
            static fn (Builder $builder, DateTimeImmutable $issuedAt): Builder => $builder
                ->expiresAt($issuedAt->modify('-10 minutes'))
                ->relatedTo((string) $token->getUserId())
                ->identifiedBy((string) $token->getId()),
        )->toString();

        $this->expectException(RequiredConstraintsViolated::class);
        $this->expectExceptionMessage('The token violates some mandatory constraints, details:
- The token is expired');

        $tokenService->getUserIdFromToken($tokenAsString);
    }

    public function test_get_user_from_token_not_found(): void
    {
        $key = base64_encode($this->generateRandomString());
        $signer = new Sha256();
        $repository = new MockedTokenRepository();

        $tokenService = new JWTTokenService($key, $signer, $repository);

        $clock = new FrozenClock((new DateTimeImmutable()));
        $signerKey = InMemory::base64Encoded($key);

        $userId = 2;
        $token = new Token(null, $userId);

        $tokenAsString = (new JwtFacade(null, $clock))->issue($signer, $signerKey,
            static fn (Builder $builder, DateTimeImmutable $issuedAt): Builder => $builder
                ->expiresAt($issuedAt->modify('+10 minutes'))
                ->relatedTo((string) $token->getUserId())
                ->identifiedBy((string) $token->getId()),
        )->toString();

        $this->expectException(TokenNotFound::class);
        $this->expectExceptionMessage('Authentication token not found.');

        $tokenService->getUserIdFromToken($tokenAsString);
    }

    public function test_revoking_token(): void
    {
        $key = base64_encode($this->generateRandomString());
        $signer = new Sha256();
        $repository = new MockedTokenRepository();

        $tokenService = new JWTTokenService($key, $signer, $repository);

        $clock = new FrozenClock((new DateTimeImmutable()));
        $signerKey = InMemory::base64Encoded($key);

        $userId = 2;
        $token = $repository->save(new Token(null, $userId));

        $this->assertNotNull(
            $repository->findById($token->getId()),
        );

        $tokenAsString = (new JwtFacade(null, $clock))->issue($signer, $signerKey,
            static fn (Builder $builder, DateTimeImmutable $issuedAt): Builder => $builder
                ->expiresAt($issuedAt->modify('+10 minutes'))
                ->relatedTo((string) $token->getUserId())
                ->identifiedBy((string) $token->getId()),
        )->toString();

        $tokenService->revokingToken($tokenAsString);
        $this->assertNull(
            $repository->findById($token->getId()),
        );
    }

    public function test_revoking_token_not_found(): void
    {
        $key = base64_encode($this->generateRandomString());
        $signer = new Sha256();
        $repository = new MockedTokenRepository();

        $tokenService = new JWTTokenService($key, $signer, $repository);

        $clock = new FrozenClock((new DateTimeImmutable()));
        $signerKey = InMemory::base64Encoded($key);

        $userId = 2;
        $token = new Token(null, $userId);

        $tokenAsString = (new JwtFacade(null, $clock))->issue($signer, $signerKey,
            static fn (Builder $builder, DateTimeImmutable $issuedAt): Builder => $builder
                ->expiresAt($issuedAt->modify('+10 minutes'))
                ->relatedTo((string) $token->getUserId())
                ->identifiedBy((string) $token->getId()),
        )->toString();

        $this->expectException(TokenNotFound::class);
        $this->expectExceptionMessage('Authentication token not found.');

        $tokenService->revokingToken($tokenAsString);
    }
}
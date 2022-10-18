<?php

declare(strict_types=1);

namespace Romanpravda\KcTestTask\Domains\Token;

use DateTimeImmutable;
use Lcobucci\Clock\FrozenClock;
use Lcobucci\JWT\Builder;
use Lcobucci\JWT\JwtFacade;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token\RegisteredClaims;
use Lcobucci\JWT\UnencryptedToken;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;
use Romanpravda\KcTestTask\Exceptions\Domains\Token\TokenNotFound;

final class JWTTokenService implements TokenServiceInterface
{
    /**
     * Key for sign token.
     *
     * @var string
     */
    private string $key;

    /**
     * Token's signer.
     *
     * @var \Lcobucci\JWT\Signer
     */
    private Signer $signer;

    /**
     * Token's repository.
     *
     * @var \Romanpravda\KcTestTask\Domains\Token\TokenRepositoryInterface
     */
    private TokenRepositoryInterface $repository;

    /**
     * TokenService constructor.
     *
     * @param string $key
     * @param \Lcobucci\JWT\Signer $signer
     * @param \Romanpravda\KcTestTask\Domains\Token\TokenRepositoryInterface $repository
     */
    public function __construct(string $key, Signer $signer, TokenRepositoryInterface $repository)
    {
        $this->key = $key;
        $this->signer = $signer;
        $this->repository = $repository;
    }

    /**
     * Issuing JWT token string for user with given ID.
     *
     * @param int $userId
     *
     * @return string
     *
     * @throws \Romanpravda\KcTestTask\Exceptions\Repositories\PDOException
     */
    public function issueTokenForUserId(int $userId): string
    {
        $clock = new FrozenClock(new DateTimeImmutable());
        $key = InMemory::base64Encoded($this->key);

        $token = $this->repository->save(new Token(null, $userId));

        return (new JwtFacade(null, $clock))->issue(
            $this->signer,
            $key,
            static fn (Builder $builder, DateTimeImmutable $issuedAt): Builder => $builder
                ->expiresAt($issuedAt->modify('+10 minutes'))
                ->relatedTo((string) $token->getUserId())
                ->identifiedBy((string) $token->getId()),
        )->toString();
    }

    /**
     * Retrieving user's ID from JWT token string.
     *
     * @param string $jwt
     *
     * @return int
     *
     * @throws \Romanpravda\KcTestTask\Exceptions\Domains\Token\TokenNotFound
     * @throws \Romanpravda\KcTestTask\Exceptions\Repositories\PDOException
     */
    public function getUserIdFromToken(string $jwt): int
    {
        $token = $this->parsingJWTTokenString($jwt);

        if (is_null($this->repository->findById((int) $token->claims()->get(RegisteredClaims::ID)))) {
            throw TokenNotFound::create();
        }

        return (int) $token->claims()->get(RegisteredClaims::SUBJECT);
    }

    /**
     * Revoking JWT token from string.
     *
     * @param string $jwt
     *
     * @return bool
     *
     * @throws \Romanpravda\KcTestTask\Exceptions\Domains\Token\TokenNotFound
     * @throws \Romanpravda\KcTestTask\Exceptions\Repositories\PDOException
     */
    public function revokingToken(string $jwt): bool
    {
        $token = $this->parsingJWTTokenString($jwt);

        $tokenModel = $this->repository->findById((int) $token->claims()->get(RegisteredClaims::ID));
        if (is_null($tokenModel)) {
            throw TokenNotFound::create();
        }

        return $this->repository->delete($tokenModel);
    }

    /**
     * Parsing JWT token string.
     *
     * @param string $jwt
     *
     * @return \Lcobucci\JWT\UnencryptedToken
     */
    private function parsingJWTTokenString(string $jwt): UnencryptedToken
    {
        $clock = new FrozenClock(new DateTimeImmutable());
        $key = InMemory::base64Encoded($this->key);

        return (new JwtFacade())->parse(
            $jwt,
            new SignedWith($this->signer, $key),
            new StrictValidAt($clock),
        );
    }
}

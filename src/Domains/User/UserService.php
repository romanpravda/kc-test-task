<?php

declare(strict_types=1);

namespace Romanpravda\KcTestTask\Domains\User;

use Romanpravda\KcTestTask\Domains\Token\TokenServiceInterface;
use Romanpravda\KcTestTask\Exceptions\Domains\User\WrongCredentialsException;
use Romanpravda\KcTestTask\Support\Criteria\Criteria;
use Romanpravda\KcTestTask\Support\Criteria\Filters\EqualsFilter;
use Romanpravda\KcTestTask\Support\Criteria\Order;

final class UserService
{
    /**
     * User repository.
     *
     * @var \Romanpravda\KcTestTask\Domains\User\UserRepositoryInterface
     */
    private UserRepositoryInterface $repository;

    /**
     * User's token service.
     *
     * @var \Romanpravda\KcTestTask\Domains\Token\TokenServiceInterface
     */
    private TokenServiceInterface $tokenService;

    /**
     * UserService constructor.
     *
     * @param \Romanpravda\KcTestTask\Domains\User\UserRepositoryInterface $repository
     * @param \Romanpravda\KcTestTask\Domains\Token\TokenServiceInterface $tokenService
     */
    public function __construct(UserRepositoryInterface $repository, TokenServiceInterface $tokenService)
    {
        $this->repository = $repository;
        $this->tokenService = $tokenService;
    }

    /**
     * Checking username and password and issuing token for user.
     *
     * @param string $username
     * @param string $password
     *
     * @return string
     *
     * @throws \Romanpravda\KcTestTask\Exceptions\Domains\User\WrongCredentialsException
     * @throws \Romanpravda\KcTestTask\Exceptions\Repositories\PDOException
     */
    public function authenticateUser(string $username, string $password): string
    {
        $filter = new EqualsFilter('username', $username);
        $criteria = new Criteria($filter, Order::none(), 1);

        $users = $this->repository->findByCriteria($criteria);
        if ($users === []) {
            throw WrongCredentialsException::create();
        }

        $user = $users[0];
        if (!$user->checkPassword($password)) {
            throw WrongCredentialsException::create();
        }

        return $this->tokenService->issueTokenForUserId($user->getId());
    }
}
<?php

declare(strict_types=1);

namespace Romanpravda\KcTestTask\Infrastructure\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Romanpravda\KcTestTask\Domains\User\UserService;
use Romanpravda\KcTestTask\Exceptions\Domains\User\WrongCredentialsException;
use Romanpravda\KcTestTask\Exceptions\Repositories\PDOException;
use Romanpravda\KcTestTask\Infrastructure\Http\Factories\Response\ResponseFactoryInterface;
use Romanpravda\KcTestTask\Infrastructure\Http\Response;

final class UserAuthenticateAction
{
    /**
     * User's service.
     *
     * @var \Romanpravda\KcTestTask\Domains\User\UserService
     */
    private UserService $service;

    /**
     * Response's factory.
     *
     * @var \Romanpravda\KcTestTask\Infrastructure\Http\Factories\Response\ResponseFactoryInterface
     */
    private ResponseFactoryInterface $responseFactory;

    /**
     * UserAuthenticateAction constructor.
     *
     * @param \Romanpravda\KcTestTask\Domains\User\UserService $service
     * @param \Romanpravda\KcTestTask\Infrastructure\Http\Factories\Response\ResponseFactoryInterface $responseFactory
     */
    public function __construct(UserService $service, ResponseFactoryInterface $responseFactory)
    {
        $this->service = $service;
        $this->responseFactory = $responseFactory;
    }

    /**
     * User's authentication action.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $username = $request->getAttribute('username');
            $password = $request->getAttribute('password');

            $token = $this->service->authenticateUser($username, $password);

            return $this->responseFactory->create(new Response([
                'data' => [
                    'token' => $token,
                ],
            ]));
        } catch (WrongCredentialsException $exception) {
            return $this->responseFactory->create((new Response([
                'error' => $exception->getMessage(),
                'trace' => $exception->getTrace(),
            ]))->setHttpCode(401));
        } catch (PDOException $exception) {
            return $this->responseFactory->create((new Response([
                'error' => $exception->getMessage(),
                'trace' => $exception->getTrace(),
            ]))->setHttpCode(500));
        }
    }
}
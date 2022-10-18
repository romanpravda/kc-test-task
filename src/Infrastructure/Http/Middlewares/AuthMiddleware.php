<?php

declare(strict_types=1);

namespace Romanpravda\KcTestTask\Infrastructure\Http\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Romanpravda\KcTestTask\Domains\Token\TokenServiceInterface;
use Romanpravda\KcTestTask\Domains\User\UserRepositoryInterface;
use Romanpravda\KcTestTask\Exceptions\Domains\Token\TokenNotFound;
use Romanpravda\KcTestTask\Exceptions\Repositories\PDOException;
use Romanpravda\KcTestTask\Infrastructure\Http\Factories\Response\ResponseFactoryInterface;
use Romanpravda\KcTestTask\Infrastructure\Http\Response;

final class AuthMiddleware implements MiddlewareInterface
{
    /**
     * Token's service.
     *
     * @var \Romanpravda\KcTestTask\Domains\Token\TokenServiceInterface
     */
    private TokenServiceInterface $tokenService;

    /**
     * User's repository.
     *
     * @var \Romanpravda\KcTestTask\Domains\User\UserRepositoryInterface
     */
    private UserRepositoryInterface $userRepository;

    /**
     * Response's factory.
     *
     * @var \Romanpravda\KcTestTask\Infrastructure\Http\Factories\Response\ResponseFactoryInterface
     */
    private ResponseFactoryInterface $responseFactory;

    /**
     * AuthMiddleware constructor.
     *
     * @param \Romanpravda\KcTestTask\Domains\Token\TokenServiceInterface $tokenService
     * @param \Romanpravda\KcTestTask\Domains\User\UserRepositoryInterface $userRepository
     * @param \Romanpravda\KcTestTask\Infrastructure\Http\Factories\Response\ResponseFactoryInterface $responseFactory
     */
    public function __construct(TokenServiceInterface $tokenService, UserRepositoryInterface $userRepository, ResponseFactoryInterface $responseFactory)
    {
        $this->tokenService = $tokenService;
        $this->userRepository = $userRepository;
        $this->responseFactory = $responseFactory;
    }

    /**
     * Process an incoming server request.
     *
     * Processes an incoming server request in order to produce a response.
     * If unable to produce the response itself, it may delegate to the provided
     * request handler to do so.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$request->hasHeader('Authorization')) {
            return $this->responseFactory->create((new Response([]))->setHttpCode(401));
        }

        $headerValue = $request->getHeader('Authorization')[0] ?? '';
        if (strpos($headerValue, 'Bearer') === false) {
            return $this->responseFactory->create((new Response([]))->setHttpCode(401));
        }

        try {
            $token = str_replace('Bearer ', '', $headerValue);
            $userId = $this->tokenService->getUserIdFromToken($token);
            $user = $this->userRepository->findById($userId);
            if (is_null($user)) {
                $response = $this->responseFactory->create((new Response([]))->setHttpCode(401));
            } else {
                $response = $handler->handle($request->withAttribute('token', $token)->withAttribute('user', $user));
            }
        } catch (TokenNotFound $e) {
            $response = $this->responseFactory->create((new Response([]))->setHttpCode(401));
        } catch (PDOException $e) {
            $response = $this->responseFactory->create((new Response([
                'error' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ]))->setHttpCode(500));
        }

        return $response;
    }
}
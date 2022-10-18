<?php

declare(strict_types=1);

namespace Romanpravda\KcTestTask\Infrastructure\Actions;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Romanpravda\KcTestTask\Domains\Token\TokenServiceInterface;
use Romanpravda\KcTestTask\Exceptions\Domains\Token\TokenNotFound;
use Romanpravda\KcTestTask\Exceptions\Repositories\PDOException;
use Romanpravda\KcTestTask\Infrastructure\Http\Factories\Response\ResponseFactoryInterface;
use Romanpravda\KcTestTask\Infrastructure\Http\Response;

/**
 * @codeCoverageIgnore
 */
final class UserLogoutAction
{
    /**
     * Token's service.
     *
     * @var \Romanpravda\KcTestTask\Domains\Token\TokenServiceInterface
     */
    private TokenServiceInterface $service;

    /**
     * Response's factory.
     *
     * @var \Romanpravda\KcTestTask\Infrastructure\Http\Factories\Response\ResponseFactoryInterface
     */
    private ResponseFactoryInterface $responseFactory;

    /**
     * UserLogoutAction constructor.
     *
     * @param \Romanpravda\KcTestTask\Domains\Token\TokenServiceInterface $service
     * @param \Romanpravda\KcTestTask\Infrastructure\Http\Factories\Response\ResponseFactoryInterface $responseFactory
     */
    public function __construct(TokenServiceInterface $service, ResponseFactoryInterface $responseFactory)
    {
        $this->service = $service;
        $this->responseFactory = $responseFactory;
    }

    /**
     * User logout action.
     *
     * @param \Psr\Http\Message\ServerRequestInterface $request
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $token = $request->getAttribute('token');
            if (is_null($token)) {
                throw TokenNotFound::create();
            }

            return $this->responseFactory->create(new Response([
                'data' => [
                    'success' => $this->service->revokingToken($token),
                ],
            ]));
        } catch (TokenNotFound $exception) {
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

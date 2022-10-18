<?php

declare(strict_types=1);

namespace Tests\Mocks;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Romanpravda\KcTestTask\Infrastructure\Http\Factories\Response\ResponseFactoryInterface;
use Romanpravda\KcTestTask\Infrastructure\Http\Response;

final class MockedHandler implements RequestHandlerInterface
{
    /**
     * Handled request.
     *
     * @var \Psr\Http\Message\ServerRequestInterface|null
     */
    private ?ServerRequestInterface $request = null;

    /**
     * Response's factory.
     *
     * @var \Romanpravda\KcTestTask\Infrastructure\Http\Factories\Response\ResponseFactoryInterface
     */
    private ResponseFactoryInterface $responseFactory;

    /**
     * MockedHandler constructor.
     *
     * @param \Romanpravda\KcTestTask\Infrastructure\Http\Factories\Response\ResponseFactoryInterface $responseFactory
     */
    public function __construct(ResponseFactoryInterface $responseFactory)
    {
        $this->responseFactory = $responseFactory;
    }

    /**
     * Handles a request and produces a response.
     *
     * May call other collaborating code to generate the response.
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $this->request = $request;

        return $this->responseFactory->create(new Response([]));
    }

    /**
     * Retrieve handled request.
     *
     * @return \Psr\Http\Message\ServerRequestInterface|null
     */
    public function getRequest(): ?ServerRequestInterface
    {
        return $this->request;
    }
}
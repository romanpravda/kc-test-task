<?php

declare(strict_types=1);

namespace Romanpravda\KcTestTask\Infrastructure\Http\Factories\Response;

use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseInterface;
use Romanpravda\KcTestTask\Infrastructure\Http\Response;

final class PSR17ResponseFactory implements ResponseFactoryInterface
{
    /**
     * Base response factory.
     *
     * @var \Nyholm\Psr7\Factory\Psr17Factory
     */
    private Psr17Factory $psr17Factory;

    /**
     * PSR17ResponseFactory constructor.
     *
     * @param \Nyholm\Psr7\Factory\Psr17Factory $psr17Factory
     */
    public function __construct(Psr17Factory $psr17Factory)
    {
        $this->psr17Factory = $psr17Factory;
    }

    /**
     * Create PSR-7 response from application's response.
     *
     * @param \Romanpravda\KcTestTask\Infrastructure\Http\Response $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     *
     * @throws \JsonException
     */
    public function create(Response $response): ResponseInterface
    {
        $responseBody = $this->psr17Factory->createStream($response->getResponseDataAsJsonString());
        $psrResponse = $this->psr17Factory->createResponse($response->getHttpCode())->withBody($responseBody);

        foreach ($response->getHeaders() as $headerName => $headerValue) {
            $psrResponse = $psrResponse->withHeader($headerName, $headerValue);
        }

        return $psrResponse;
    }
}

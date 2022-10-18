<?php

declare(strict_types=1);

namespace Romanpravda\KcTestTask\Infrastructure\Http\Middlewares;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Romanpravda\KcTestTask\Infrastructure\Http\Factories\Response\ResponseFactoryInterface;
use Romanpravda\KcTestTask\Infrastructure\Http\Response;

final class DecodeJsonBodyMiddleware implements MiddlewareInterface
{
    /**
     * Response's factory.
     *
     * @var \Romanpravda\KcTestTask\Infrastructure\Http\Factories\Response\ResponseFactoryInterface
     */
    private ResponseFactoryInterface $responseFactory;

    /**
     * DecodeJsonBodyMiddleware constructor.
     *
     * @param \Romanpravda\KcTestTask\Infrastructure\Http\Factories\Response\ResponseFactoryInterface $responseFactory
     */
    public function __construct(ResponseFactoryInterface $responseFactory)
    {
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
        if (!$request->hasHeader('Content-Type') || strpos($request->getHeader('Content-Type')[0], 'application/json') === false) {
            return $handler->handle($request);
        }

        try {
            $body = $request->getBody()->getContents();
            $decodedBody = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

            foreach ($decodedBody as $key => $value) {
                $request = $request->withAttribute($key, $value);
            }

            $response = $handler->handle($request);
        } catch (\JsonException $e) {
            $response = $this->responseFactory->create((new Response([
                'error' => $e->getMessage(),
                'trace' => $e->getTrace(),
            ]))->setHttpCode(400));
        }

        return $response;
    }
}

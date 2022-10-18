<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Http\Middlewares;

use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use PHPUnit\Framework\TestCase;
use Romanpravda\KcTestTask\Infrastructure\Http\Factories\Response\PSR17ResponseFactory;
use Romanpravda\KcTestTask\Infrastructure\Http\Middlewares\DecodeJsonBodyMiddleware;
use Tests\Mocks\MockedHandler;

final class DecodeJsonBodyMiddlewareTest extends TestCase
{
    public function test_decode_json_body_middleware(): void
    {
        $psr17Factory = new Psr17Factory();
        $psr17ResponseFactory = new PSR17ResponseFactory($psr17Factory);

        $middleware = new DecodeJsonBodyMiddleware($psr17ResponseFactory);

        $data = [
            'foo' => 'bar',
        ];
        $jsonString = json_encode($data, JSON_THROW_ON_ERROR);
        $stream = $psr17Factory->createStream($jsonString);
        $stream->rewind();

        $requestCreator = new ServerRequestCreator($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
        $request = $requestCreator->fromArrays([
            'REQUEST_METHOD' => 'GET',
        ], [
            'Content-Type' => [
                'application/json',
            ],
        ], [], [], null, [], $stream);
        $handler = new MockedHandler($psr17ResponseFactory);

        $middleware->process($request, $handler);

        $this->assertNotNull(
            $handler->getRequest(),
        );
        $this->assertEquals(
            $data['foo'],
            $handler->getRequest()->getAttribute('foo'),
        );
    }

    public function test_decode_json_body_middleware_no_json_body(): void
    {
        $psr17Factory = new Psr17Factory();
        $psr17ResponseFactory = new PSR17ResponseFactory($psr17Factory);

        $middleware = new DecodeJsonBodyMiddleware($psr17ResponseFactory);

        $requestCreator = new ServerRequestCreator($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
        $request = $requestCreator->fromArrays([
            'REQUEST_METHOD' => 'GET',
        ]);
        $handler = new MockedHandler($psr17ResponseFactory);

        $middleware->process($request, $handler);

        $this->assertNotNull(
            $handler->getRequest(),
        );
        $this->assertEquals(
            $request->getAttributes(),
            $handler->getRequest()->getAttributes(),
        );
    }

    public function test_decode_json_body_middleware_wrong_body(): void
    {
        $psr17Factory = new Psr17Factory();
        $psr17ResponseFactory = new PSR17ResponseFactory($psr17Factory);

        $middleware = new DecodeJsonBodyMiddleware($psr17ResponseFactory);

        $requestCreator = new ServerRequestCreator($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
        $request = $requestCreator->fromArrays([
            'REQUEST_METHOD' => 'GET',
        ], [
            'Content-Type' => [
                'application/json',
            ],
        ], [], [], null, [], "");
        $handler = new MockedHandler($psr17ResponseFactory);

        $response = $middleware->process($request, $handler);

        $this->assertNull(
            $handler->getRequest(),
        );
        $this->assertEquals(
            400,
            $response->getStatusCode(),
        );
    }
}

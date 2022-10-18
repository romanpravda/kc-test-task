<?php

declare(strict_types=1);

namespace Tests\Unit\Infrastructure\Http\Middlewares;

use Faker\Factory;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7Server\ServerRequestCreator;
use PHPUnit\Framework\TestCase;
use Romanpravda\KcTestTask\Domains\User\User;
use Romanpravda\KcTestTask\Infrastructure\Http\Factories\Response\PSR17ResponseFactory;
use Romanpravda\KcTestTask\Infrastructure\Http\Middlewares\AuthMiddleware;
use Tests\Mocks\MockedHandler;
use Tests\Mocks\MockedTokenService;
use Tests\Mocks\MockedUserRepository;

final class AuthMiddlewareTest extends TestCase
{
    public function test_auth_middleware(): void
    {
        $faker = Factory::create(Factory::DEFAULT_LOCALE);

        $psr17Factory = new Psr17Factory();
        $psr17ResponseFactory = new PSR17ResponseFactory($psr17Factory);

        $tokenService = new MockedTokenService();
        $userRepository = new MockedUserRepository();

        $user = $userRepository->save(new User(null, $faker->email(), $faker->userName(), $faker->password()));
        $token = $tokenService->issueTokenForUserId($user->getId());

        $middleware = new AuthMiddleware($tokenService, $userRepository, $psr17ResponseFactory);

        $requestCreator = new ServerRequestCreator($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
        $request = $requestCreator->fromArrays([
            'REQUEST_METHOD' => 'GET',
        ], [
            'Authorization' => [
                sprintf('Bearer %s', $token),
            ]
        ]);
        $handler = new MockedHandler($psr17ResponseFactory);

        $middleware->process($request, $handler);

        $this->assertNotNull(
            $handler->getRequest(),
        );
        $this->assertEquals(
            $token,
            $handler->getRequest()->getAttribute('token'),
        );
        $this->assertEquals(
            $user,
            $handler->getRequest()->getAttribute('user'),
        );
    }

    public function test_auth_middleware_no_header(): void
    {
        $psr17Factory = new Psr17Factory();
        $psr17ResponseFactory = new PSR17ResponseFactory($psr17Factory);

        $tokenService = new MockedTokenService();
        $userRepository = new MockedUserRepository();

        $middleware = new AuthMiddleware($tokenService, $userRepository, $psr17ResponseFactory);

        $requestCreator = new ServerRequestCreator($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
        $request = $requestCreator->fromArrays([
            'REQUEST_METHOD' => 'GET',
        ]);
        $handler = new MockedHandler($psr17ResponseFactory);

        $response = $middleware->process($request, $handler);

        $this->assertNull(
            $handler->getRequest(),
        );
        $this->assertEquals(
            401,
            $response->getStatusCode(),
        );
    }

    public function test_auth_middleware_not_bearer_header(): void
    {
        $psr17Factory = new Psr17Factory();
        $psr17ResponseFactory = new PSR17ResponseFactory($psr17Factory);

        $tokenService = new MockedTokenService();
        $userRepository = new MockedUserRepository();

        $middleware = new AuthMiddleware($tokenService, $userRepository, $psr17ResponseFactory);

        $requestCreator = new ServerRequestCreator($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
        $request = $requestCreator->fromArrays([
            'REQUEST_METHOD' => 'GET',
        ], [
            'Authorization' => [
                'FooBar',
            ]
        ]);
        $handler = new MockedHandler($psr17ResponseFactory);

        $response = $middleware->process($request, $handler);

        $this->assertNull(
            $handler->getRequest(),
        );
        $this->assertEquals(
            401,
            $response->getStatusCode(),
        );
    }

    public function test_auth_middleware_token_not_found(): void
    {
        $psr17Factory = new Psr17Factory();
        $psr17ResponseFactory = new PSR17ResponseFactory($psr17Factory);

        $tokenService = new MockedTokenService();
        $userRepository = new MockedUserRepository();

        $token = 'FooBar';

        $middleware = new AuthMiddleware($tokenService, $userRepository, $psr17ResponseFactory);

        $requestCreator = new ServerRequestCreator($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
        $request = $requestCreator->fromArrays([
            'REQUEST_METHOD' => 'GET',
        ], [
            'Authorization' => [
                sprintf('Bearer %s', $token),
            ]
        ]);
        $handler = new MockedHandler($psr17ResponseFactory);

        $response = $middleware->process($request, $handler);

        $this->assertNull(
            $handler->getRequest(),
        );
        $this->assertEquals(
            401,
            $response->getStatusCode(),
        );
    }

    public function test_auth_middleware_no_user(): void
    {
        $psr17Factory = new Psr17Factory();
        $psr17ResponseFactory = new PSR17ResponseFactory($psr17Factory);

        $tokenService = new MockedTokenService();
        $userRepository = new MockedUserRepository();

        $token = $tokenService->issueTokenForUserId(1);

        $middleware = new AuthMiddleware($tokenService, $userRepository, $psr17ResponseFactory);

        $requestCreator = new ServerRequestCreator($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
        $request = $requestCreator->fromArrays([
            'REQUEST_METHOD' => 'GET',
        ], [
            'Authorization' => [
                sprintf('Bearer %s', $token),
            ]
        ]);
        $handler = new MockedHandler($psr17ResponseFactory);

        $response = $middleware->process($request, $handler);

        $this->assertNull(
            $handler->getRequest(),
        );
        $this->assertEquals(
            401,
            $response->getStatusCode(),
        );
    }
}

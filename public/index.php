<?php

declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

$appConfig = require __DIR__.'/../config/app.php';
$authConfig = require __DIR__.'/../config/auth.php';
$databaseConfig = require __DIR__.'/../config/database.php';

$host = $databaseConfig['host'];
$port = $databaseConfig['port'];
$database = $databaseConfig['database'];
$username = $databaseConfig['username'];
$password = $databaseConfig['password'];

$dsn = sprintf('mysql:host=%s:%d;dbname=%s;charset=UTF8', $host, $port, $database);

$container = new \League\Container\Container();

$container->add(\PDO::class)
    ->addArgument(new \League\Container\Argument\Literal\StringArgument($dsn))
    ->addArgument(new \League\Container\Argument\Literal\StringArgument($username))
    ->addArgument(new \League\Container\Argument\Literal\StringArgument($password));

$container->add(\Nyholm\Psr7\Factory\Psr17Factory::class);

$container->add(\Nyholm\Psr7Server\ServerRequestCreator::class)
    ->addArgument(\Nyholm\Psr7\Factory\Psr17Factory::class)
    ->addArgument(\Nyholm\Psr7\Factory\Psr17Factory::class)
    ->addArgument(\Nyholm\Psr7\Factory\Psr17Factory::class)
    ->addArgument(\Nyholm\Psr7\Factory\Psr17Factory::class);

$container->add(\Romanpravda\KcTestTask\Infrastructure\Http\Factories\Response\ResponseFactoryInterface::class, static function () use ($container) {
    /** @var \Nyholm\Psr7\Factory\Psr17Factory$psr17Factory */
    $psr17Factory = $container->get(\Nyholm\Psr7\Factory\Psr17Factory::class);

    return new \Romanpravda\KcTestTask\Infrastructure\Http\Factories\Response\PSR17ResponseFactory($psr17Factory);
});

$container->add(\Romanpravda\KcTestTask\Support\PasswordHasher\PasswordHasherInterface::class, static function () use ($authConfig) {
    return new \Romanpravda\KcTestTask\Support\PasswordHasher\PasswordHasher($authConfig['algorithm']);
});

$container->add(\Romanpravda\KcTestTask\Domains\User\UserFactory::class)
    ->addArgument(\Romanpravda\KcTestTask\Support\PasswordHasher\PasswordHasherInterface::class);

$container->add(\Romanpravda\KcTestTask\Domains\Token\TokenRepositoryInterface::class, static function () use ($container) {
    /** @var \PDO $PDO */
    $PDO = $container->get(\PDO::class);

    return new \Romanpravda\KcTestTask\Infrastructure\Database\Repositories\PDOTokenRepository($PDO);
});

$container->add(\Romanpravda\KcTestTask\Domains\User\UserRepositoryInterface::class, static function () use ($container) {
    /** @var \PDO $PDO */
    $PDO = $container->get(\PDO::class);

    /** @var \Romanpravda\KcTestTask\Domains\User\UserFactory $userFactory */
    $userFactory = $container->get(\Romanpravda\KcTestTask\Domains\User\UserFactory::class);

    return new \Romanpravda\KcTestTask\Infrastructure\Database\Repositories\PDOUserRepository($PDO, $userFactory);
});

$container->add(\Romanpravda\KcTestTask\Domains\Student\StudentRepositoryInterface::class, static function () use ($container) {
    /** @var \PDO $PDO */
    $PDO = $container->get(\PDO::class);

    return new \Romanpravda\KcTestTask\Infrastructure\Database\Repositories\PDOStudentRepository($PDO);
});

$container->add(\Lcobucci\JWT\Signer::class, static function () {
    return new \Lcobucci\JWT\Signer\Hmac\Sha256();
});

$container->add(\Romanpravda\KcTestTask\Domains\Token\TokenServiceInterface::class, static function () use ($container, $appConfig) {
    /** @var \Lcobucci\JWT\Signer $signer */
    $signer = $container->get(\Lcobucci\JWT\Signer::class);

    /** @var \Romanpravda\KcTestTask\Domains\Token\TokenRepositoryInterface $tokenRepository */
    $tokenRepository = $container->get(\Romanpravda\KcTestTask\Domains\Token\TokenRepositoryInterface::class);

    return new \Romanpravda\KcTestTask\Domains\Token\JWTTokenService($appConfig['key'], $signer, $tokenRepository);
});

$container->add(\Romanpravda\KcTestTask\Domains\User\UserService::class)
    ->addArgument(\Romanpravda\KcTestTask\Domains\User\UserRepositoryInterface::class)
    ->addArgument(\Romanpravda\KcTestTask\Domains\Token\TokenServiceInterface::class);

$container->add(\Romanpravda\KcTestTask\Infrastructure\Actions\UserAuthenticateAction::class)
    ->addArgument(\Romanpravda\KcTestTask\Domains\User\UserService::class)
    ->addArgument(\Romanpravda\KcTestTask\Infrastructure\Http\Factories\Response\ResponseFactoryInterface::class);

$container->add(\Romanpravda\KcTestTask\Infrastructure\Actions\UserStudentsListAction::class)
    ->addArgument(\Romanpravda\KcTestTask\Domains\Student\StudentRepositoryInterface::class)
    ->addArgument(\Romanpravda\KcTestTask\Infrastructure\Http\Factories\Response\ResponseFactoryInterface::class);

$container->add(\Romanpravda\KcTestTask\Infrastructure\Actions\UserLogoutAction::class)
    ->addArgument(\Romanpravda\KcTestTask\Domains\Token\TokenServiceInterface::class)
    ->addArgument(\Romanpravda\KcTestTask\Infrastructure\Http\Factories\Response\ResponseFactoryInterface::class);

$container->add(\Romanpravda\KcTestTask\Infrastructure\Http\Middlewares\AuthMiddleware::class)
    ->addArgument(\Romanpravda\KcTestTask\Domains\Token\TokenServiceInterface::class)
    ->addArgument(\Romanpravda\KcTestTask\Domains\User\UserRepositoryInterface::class)
    ->addArgument(\Romanpravda\KcTestTask\Infrastructure\Http\Factories\Response\ResponseFactoryInterface::class);

$container->add(\Romanpravda\KcTestTask\Infrastructure\Http\Middlewares\DecodeJsonBodyMiddleware::class)
    ->addArgument(\Romanpravda\KcTestTask\Infrastructure\Http\Factories\Response\ResponseFactoryInterface::class);

try {
    /** @var \Psr\Http\Message\ServerRequestInterface $request */
    $request = $container->get(\Nyholm\Psr7Server\ServerRequestCreator::class)->fromGlobals();

    $router = new League\Route\Router;
    $router->middleware($container->get(\Romanpravda\KcTestTask\Infrastructure\Http\Middlewares\DecodeJsonBodyMiddleware::class));

    $router->post('/auth', $container->get(\Romanpravda\KcTestTask\Infrastructure\Actions\UserAuthenticateAction::class));
    $router->get('/users', $container->get(\Romanpravda\KcTestTask\Infrastructure\Actions\UserStudentsListAction::class))->middleware($container->get(\Romanpravda\KcTestTask\Infrastructure\Http\Middlewares\AuthMiddleware::class));
    $router->delete('/auth', $container->get(\Romanpravda\KcTestTask\Infrastructure\Actions\UserLogoutAction::class))->middleware($container->get(\Romanpravda\KcTestTask\Infrastructure\Http\Middlewares\AuthMiddleware::class));

    $response = $router->dispatch($request);
    (new \Laminas\HttpHandlerRunner\Emitter\SapiEmitter())->emit($response);
} catch (\Throwable $throwable) {
    /** @var \Romanpravda\KcTestTask\Infrastructure\Http\Factories\Response\ResponseFactoryInterface $factory */
    $factory = $container->get(\Romanpravda\KcTestTask\Infrastructure\Http\Factories\Response\ResponseFactoryInterface::class);
    (new \Laminas\HttpHandlerRunner\Emitter\SapiEmitter())->emit($factory->create((new \Romanpravda\KcTestTask\Infrastructure\Http\Response([
        'error' => $throwable->getMessage(),
        'trace' => $throwable->getTrace(),
    ]))->setHttpCode(500)));
}
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

$passwordHasher = new \Romanpravda\KcTestTask\Support\PasswordHasher\PasswordHasher($authConfig['algorithm']);
$userFactory = new \Romanpravda\KcTestTask\Domains\User\UserFactory($passwordHasher);

$pdo = new PDO($dsn, $username, $password);

$usersRepository = new \Romanpravda\KcTestTask\Infrastructure\Database\Repositories\PDOUserRepository($pdo, $userFactory);
$studentsRepository = new \Romanpravda\KcTestTask\Infrastructure\Database\Repositories\PDOStudentRepository($pdo);

$faker = \Faker\Factory::create('en_US');

$users = [
    [
        'email' => $faker->email(),
        'username' => $faker->userName(),
        'password' => $faker->password(),
    ],
    [
        'email' => $faker->email(),
        'username' => $faker->userName(),
        'password' => $faker->password(),
    ],
];

foreach ($users as $index => $user) {
    $userModel = $userFactory->create(null, $user['email'], $user['username'], $user['password']);
    $userModel->setPassword($user['password']);
    $users[$index] = $usersRepository->save($userModel);

    echo sprintf('Created user with ID %d', $users[$index]->getId()).PHP_EOL;
    echo sprintf('Username: %s', $user['username']).PHP_EOL;
    echo sprintf('Password: %s', $user['password']).PHP_EOL.PHP_EOL.PHP_EOL;
}

$studentsFullNames = [
    sprintf('%s %s', $faker->name(), $faker->lastName()),
    sprintf('%s %s', $faker->name(), $faker->lastName()),
    sprintf('%s %s', $faker->name(), $faker->lastName()),
    sprintf('%s %s', $faker->name(), $faker->lastName()),
    sprintf('%s %s', $faker->name(), $faker->lastName()),
    sprintf('%s %s', $faker->name(), $faker->lastName()),
    sprintf('%s %s', $faker->name(), $faker->lastName()),
    sprintf('%s %s', $faker->name(), $faker->lastName()),
    sprintf('%s %s', $faker->name(), $faker->lastName()),
    sprintf('%s %s', $faker->name(), $faker->lastName()),
    sprintf('%s %s', $faker->name(), $faker->lastName()),
    sprintf('%s %s', $faker->name(), $faker->lastName()),
    sprintf('%s %s', $faker->name(), $faker->lastName()),
    sprintf('%s %s', $faker->name(), $faker->lastName()),
];

foreach ($studentsFullNames as $studentFullName) {
    $user = $users[random_int(0, 1)];

    $student = $studentsRepository->save(new \Romanpravda\KcTestTask\Domains\Student\Student(null, $user->getId(), $studentFullName, null));

    echo sprintf('Created student with ID %d for user with ID %d', $student->getId(), $user->getId()).PHP_EOL;
}
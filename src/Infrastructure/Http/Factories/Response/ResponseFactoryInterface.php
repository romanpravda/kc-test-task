<?php

declare(strict_types=1);

namespace Romanpravda\KcTestTask\Infrastructure\Http\Factories\Response;

use Psr\Http\Message\ResponseInterface;
use Romanpravda\KcTestTask\Infrastructure\Http\Response;

interface ResponseFactoryInterface
{
    /**
     * Create PSR-7 response from application's response.
     *
     * @param \Romanpravda\KcTestTask\Infrastructure\Http\Response $response
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function create(Response $response): ResponseInterface;
}
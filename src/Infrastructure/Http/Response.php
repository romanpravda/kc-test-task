<?php

declare(strict_types=1);

namespace Romanpravda\KcTestTask\Infrastructure\Http;

final class Response
{
    /**
     *
     *
     * @var array
     */
    private array $responseArray;

    /**
     *
     *
     * @var int
     */
    private int $httpCode;

    /**
     *
     *
     * @var array
     */
    private array $headers;

    public function __construct(array $responseArray, int $httpCode = 200, array $headers = [
        'Content-Type' => 'application/json',
    ])
    {
        $this->responseArray = $responseArray;
        $this->httpCode = $httpCode;
        $this->headers = $headers;
    }

    /**
     *
     *
     * @return array
     */
    public function getResponseArray(): array
    {
        return $this->responseArray;
    }

    /**
     *
     *
     * @return string
     *
     * @throws \JsonException
     */
    public function getResponseDataAsJsonString(): string
    {
        return json_encode($this->responseArray, JSON_THROW_ON_ERROR);
    }

    /**
     *
     *
     * @return int
     */
    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    /**
     *
     *
     * @param int $httpCode
     *
     * @return self
     */
    public function setHttpCode(int $httpCode): self
    {
        $this->httpCode = $httpCode;
        return $this;
    }

    /**
     *
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     *
     *
     * @param array $headers
     *
     * @return self
     */
    public function setHeaders(array $headers): self
    {
        foreach ($headers as $headerKey => $headerValue) {
            $this->headers[$headerKey] = $headerValue;
        }

        return $this;
    }

    /**
     *
     *
     * @param array $headers
     *
     * @return self
     */
    public function rewriteHeaders(array $headers): self
    {
        $this->headers = $headers;
        return $this;
    }
}
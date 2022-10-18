<?php

declare(strict_types=1);

namespace Romanpravda\KcTestTask\Infrastructure\Http;

/**
 * @codeCoverageIgnore
 */
final class Response
{
    /**
     * Response's data.
     *
     * @var array
     */
    private array $responseArray;

    /**
     * Response's HTTP status code.
     *
     * @var int
     */
    private int $httpCode;

    /**
     * Response's headers.
     *
     * @var array
     */
    private array $headers;

    public function __construct(array $responseArray, int $httpCode = 200, array $headers = [])
    {
        $this->responseArray = $responseArray;
        $this->httpCode = $httpCode;
        $this->headers = $headers;

        $this->headers['Content-Type'] = 'application/json';
    }

    /**
     * Getting response's data.
     *
     * @return array
     */
    public function getResponseArray(): array
    {
        return $this->responseArray;
    }

    /**
     * Retrieving response's data as json string.
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
     * Getting response's HTTP status code.
     *
     * @return int
     */
    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    /**
     * Setting response's HTTP status code.
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
     * Getting response's headers.
     *
     * @return array
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     * Adding new headers to response's headers.
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
     * Rewriting response's headers.
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

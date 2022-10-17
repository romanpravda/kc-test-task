<?php

declare(strict_types=1);

namespace Romanpravda\KcTestTask\Support\Criteria\Filters;

interface FilterInterface
{
    /**
     * Retrieving filter's query.
     *
     * @return string
     */
    public function getQuery(): string;

    /**
     * Retrieving filter's value.
     *
     * @return array
     */
    public function getValue(): array;
}
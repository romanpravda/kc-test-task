<?php

declare(strict_types=1);

namespace Romanpravda\KcTestTask\Support\Criteria\Filters;

final class EqualsFilter implements FilterInterface
{
    /**
     * Filter's column.
     *
     * @var string
     */
    private string $column;

    /**
     * Filter's value.
     *
     * @var mixed
     */
    private $value;

    /**
     * Key for binding value to query.
     *
     * @var string
     */
    private $keyForValue;

    /**
     * EqualsFilter constructor.
     *
     * @param string $column
     * @param $value
     */
    public function __construct(string $column, $value)
    {
        $this->column = $column;
        $this->value = $value;

        $this->makeKeyForValue();
    }

    /**
     * Retrieving filter's query.
     *
     * @return mixed
     */
    public function getQuery(): string
    {
        return sprintf('`%s` = :%s', $this->column, $this->keyForValue);
    }

    /**
     * Retrieving filter's value.
     *
     * @return array
     */
    public function getValue(): array
    {
        return [
            sprintf(':%s', $this->keyForValue) => $this->value,
        ];
    }

    /**
     * Making key for binding value to query.
     *
     * @return void
     */
    private function makeKeyForValue(): void
    {
        $this->keyForValue = md5($this->column.((string) $this->value));
    }
}
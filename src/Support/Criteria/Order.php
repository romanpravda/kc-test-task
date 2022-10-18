<?php

declare(strict_types=1);

namespace Romanpravda\KcTestTask\Support\Criteria;

final class Order
{
    /**
     * Sort field
     *
     * @var string|null
     */
    private ?string $orderBy;

    /**
     * Sort type;
     *
     * @var string
     */
    private string $orderType;

    /**
     * Order constructor.
     *
     * @param string|null $orderBy
     * @param string $orderType
     */
    public function __construct(?string $orderBy, string $orderType)
    {
        $this->orderBy = $orderBy;
        $this->orderType = $orderType;
    }

    /**
     * Ascending sort.
     *
     * @param string $orderBy
     *
     * @return static
     */
    public static function asc(string $orderBy): self
    {
        return new self($orderBy, OrderType::ASC);
    }

    /**
     * Descending sort.
     *
     * @param string $orderBy
     *
     * @return static
     */
    public static function desc(string $orderBy): self
    {
        return new self($orderBy, OrderType::DESC);
    }

    /**
     * None sort.
     *
     * @return static
     */
    public static function none(): self
    {
        return new self(null, OrderType::NONE);
    }

    /**
     * Retrieving sort field.
     *
     * @return string|null
     */
    public function getOrderBy(): ?string
    {
        return $this->orderBy;
    }

    /**
     * Retrieving sort type.
     *
     * @return string
     */
    public function getOrderType(): string
    {
        return $this->orderType;
    }

    /**
     * Check for none sort.
     *
     * @return bool
     */
    public function isNone(): bool
    {
        return $this->orderType === OrderType::NONE;
    }
}

<?php

declare(strict_types=1);

namespace Romanpravda\KcTestTask\Support\Criteria;

use Romanpravda\KcTestTask\Support\Criteria\Filters\FilterInterface;

final class Criteria
{
    /**
     * Filter data.
     *
     * @var \Romanpravda\KcTestTask\Support\Criteria\Filters\FilterInterface|null
     */
    private ?FilterInterface $filter;

    /**
     * Order data.
     *
     * @var \Romanpravda\KcTestTask\Support\Criteria\Order
     */
    private Order $order;

    /**
     * Offset for values.
     *
     * @var int|null
     */
    private ?int $offset;

    /**
     * Limit values count.
     *
     * @var int|null
     */
    private ?int $limit;

    /**
     * Criteria constructor.
     *
     * @param \Romanpravda\KcTestTask\Support\Criteria\Filters\FilterInterface|null $filter
     * @param \Romanpravda\KcTestTask\Support\Criteria\Order $order
     * @param int|null $offset
     * @param int|null $limit
     */
    public function __construct(?FilterInterface $filter, Order $order, ?int $limit = null, ?int $offset = null)
    {
        $this->filter = $filter;
        $this->order = $order;
        $this->limit = $limit;
        $this->offset = $offset;
    }

    /**
     * Check for filter presence.
     *
     * @return bool
     */
    public function hasFilter(): bool
    {
        return !is_null($this->filter);
    }

    /**
     * Retrieving filter data.
     *
     * @return \Romanpravda\KcTestTask\Support\Criteria\Filters\FilterInterface|null
     */
    public function getFilter(): ?FilterInterface
    {
        return $this->filter;
    }

    /**
     * Retrieving order data.
     *
     * @return \Romanpravda\KcTestTask\Support\Criteria\Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * Retrieving offset for values.
     *
     * @return int|null
     */
    public function getOffset(): ?int
    {
        return $this->offset;
    }

    /**
     * Retrieving limit values count.
     *
     * @return int|null
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }
}

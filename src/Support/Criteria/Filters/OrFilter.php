<?php

declare(strict_types=1);

namespace Romanpravda\KcTestTask\Support\Criteria\Filters;

final class OrFilter implements FilterInterface
{
    /**
     * Childs filters list.
     *
     * @var \Romanpravda\KcTestTask\Support\Criteria\Filters\FilterInterface[]
     */
    private array $filters;

    /**
     * OrFilter constructor.
     *
     * @param \Romanpravda\KcTestTask\Support\Criteria\Filters\FilterInterface[] $filters
     */
    public function __construct(FilterInterface... $filters)
    {
        $this->filters = $filters;
    }

    /**
     * Retrieving filter's query.
     *
     * @return string
     */
    public function getQuery(): string
    {
        $queries = [];

        foreach ($this->filters as $filter) {
            $queries[] = $filter->getQuery();
        }

        return sprintf('(%s)', implode(') OR (', $queries));
    }

    /**
     * Retrieving filter's value.
     *
     * @return array
     */
    public function getValue(): array
    {
        $values = [];

        foreach ($this->filters as $filter) {
            foreach ($filter->getValue() as $key => $value) {
                $values[$key] = $value;
            }
        }

        return $values;
    }
}
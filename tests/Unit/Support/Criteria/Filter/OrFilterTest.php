<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Criteria\Filter;

use PHPUnit\Framework\TestCase;
use Romanpravda\KcTestTask\Support\Criteria\Filters\EqualsFilter;
use Romanpravda\KcTestTask\Support\Criteria\Filters\OrFilter;

final class OrFilterTest extends TestCase
{
    public function test_and_filter(): void
    {
        $filterOne = new EqualsFilter('foobar', 'barfoo');
        $filterTwo = new EqualsFilter('foofoo', 'barbar');

        $filter = new OrFilter($filterOne, $filterTwo);

        $values = [];
        foreach ($filterOne->getValue() as $key => $value) {
            $values[$key] = $value;
        }
        foreach ($filterTwo->getValue() as $key => $value) {
            $values[$key] = $value;
        }

        $this->assertEquals(
            sprintf('(%s) OR (%s)', $filterOne->getQuery(), $filterTwo->getQuery()),
            $filter->getQuery(),
        );
        $this->assertEquals(
            $values,
            $filter->getValue(),
        );
    }
}
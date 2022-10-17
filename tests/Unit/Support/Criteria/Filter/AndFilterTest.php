<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Criteria\Filter;

use PHPUnit\Framework\TestCase;
use Romanpravda\KcTestTask\Support\Criteria\Filters\AndFilter;
use Romanpravda\KcTestTask\Support\Criteria\Filters\EqualsFilter;

final class AndFilterTest extends TestCase
{
    public function test_and_filter(): void
    {
        $filterOne = new EqualsFilter('foobar', 'barfoo');
        $filterTwo = new EqualsFilter('foofoo', 'barbar');

        $filter = new AndFilter($filterOne, $filterTwo);

        $values = [];
        foreach ($filterOne->getValue() as $key => $value) {
            $values[$key] = $value;
        }
        foreach ($filterTwo->getValue() as $key => $value) {
            $values[$key] = $value;
        }

        $this->assertEquals(
            sprintf('(%s) AND (%s)', $filterOne->getQuery(), $filterTwo->getQuery()),
            $filter->getQuery(),
        );
        $this->assertEquals(
            $values,
            $filter->getValue(),
        );
    }
}
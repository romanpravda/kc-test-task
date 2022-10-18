<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Criteria;

use PHPUnit\Framework\TestCase;
use Romanpravda\KcTestTask\Support\Criteria\Criteria;
use Romanpravda\KcTestTask\Support\Criteria\Filters\AndFilter;
use Romanpravda\KcTestTask\Support\Criteria\Filters\EqualsFilter;
use Romanpravda\KcTestTask\Support\Criteria\Order;

final class CriteriaTest extends TestCase
{
    public function test_criteria_full(): void
    {
        $filterOne = new EqualsFilter('foobar', 'barfoo');
        $filterTwo = new EqualsFilter('foofoo', 'barbar');

        $filter = new AndFilter($filterOne, $filterTwo);
        $order = Order::desc('barbar');

        $criteria = new Criteria($filter, $order, 25, 50);

        $this->assertTrue(
            $criteria->hasFilter(),
        );
        $this->assertEquals(
            $order,
            $criteria->getOrder(),
        );
        $this->assertEquals(
            $filter,
            $criteria->getFilter(),
        );
        $this->assertEquals(
            25,
            $criteria->getLimit(),
        );
        $this->assertEquals(
            50,
            $criteria->getOffset(),
        );
    }

    public function test_criteria_min(): void
    {
        $criteria = new Criteria(null, Order::none());

        $this->assertFalse(
            $criteria->hasFilter(),
        );
        $this->assertTrue(
            $criteria->getOrder()->isNone(),
        );
        $this->assertNull(
            $criteria->getLimit(),
        );
        $this->assertNull(
            $criteria->getOffset(),
        );
    }
}

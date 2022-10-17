<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Criteria;

use PHPUnit\Framework\TestCase;
use Romanpravda\KcTestTask\Support\Criteria\Order;
use Romanpravda\KcTestTask\Support\Criteria\OrderType;

final class OrderTest extends TestCase
{
    public function test_asc(): void
    {
        $orderBy = 'foobar';
        $order = Order::asc($orderBy);

        $this->assertEquals(
            OrderType::ASC,
            $order->getOrderType(),
        );
        $this->assertEquals(
            $orderBy,
            $order->getOrderBy(),
        );
        $this->assertFalse(
            $order->isNone(),
        );
    }

    public function test_desc(): void
    {
        $orderBy = 'foobar';
        $order = Order::desc($orderBy);

        $this->assertEquals(
            OrderType::DESC,
            $order->getOrderType(),
        );
        $this->assertEquals(
            $orderBy,
            $order->getOrderBy(),
        );
        $this->assertFalse(
            $order->isNone(),
        );
    }

    public function test_none(): void
    {
        $order = Order::none();

        $this->assertEquals(
            OrderType::NONE,
            $order->getOrderType(),
        );
        $this->assertNull(
            $order->getOrderBy(),
        );
        $this->assertTrue(
            $order->isNone(),
        );
    }
}
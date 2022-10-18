<?php

declare(strict_types=1);

namespace Tests\Unit\Support\Criteria\Filter;

use PHPUnit\Framework\TestCase;
use Romanpravda\KcTestTask\Support\Criteria\Filters\EqualsFilter;

final class EqualsFilterTest extends TestCase
{
    public function test_equals_filter(): void
    {
        $tcs = [
            [
                'key' => 'foo',
                'value' => 'bar',
            ],
            [
                'key' => 'foofoo',
                'value' => 'barbar',
            ],
            [
                'key' => 'example',
                'value' => 12345,
            ],
        ];

        foreach ($tcs as $tc) {
            $filter = new EqualsFilter($tc['key'], $tc['value']);

            $this->assertEquals(
                sprintf('`%s` = :%s', $tc['key'], md5($tc['key'].((string) $tc['value']))),
                $filter->getQuery(),
            );
            $this->assertEquals(
                [
                    sprintf(':%s', md5($tc['key'].((string) $tc['value']))) => $tc['value'],
                ],
                $filter->getValue(),
            );
        }
    }
}

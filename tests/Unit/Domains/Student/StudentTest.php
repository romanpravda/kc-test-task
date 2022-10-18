<?php

declare(strict_types=1);

namespace Tests\Unit\Domains\Student;

use Faker\Factory;
use PHPUnit\Framework\TestCase;
use Romanpravda\KcTestTask\Domains\Student\Student;

final class StudentTest extends TestCase
{
    public function test_student(): void
    {
        $faker = Factory::create(Factory::DEFAULT_LOCALE);

        $userId = 2;
        $fullName = sprintf('%s %s', $faker->name(), $faker->lastName());
        $group = null;

        $student = new Student(null, $userId, $fullName, $group);

        $this->assertNull(
            $student->getId(),
        );
        $this->assertEquals(
            $userId,
            $student->getUserId(),
        );
        $this->assertEquals(
            $fullName,
            $student->getFullName(),
        );
        $this->assertNull(
            $student->getGroup(),
        );

        $id = 1;
        $group = $faker->word();

        $student->setId($id);
        $student->setGroup($group);

        $this->assertEquals(
            $id,
            $student->getId(),
        );
        $this->assertEquals(
            $group,
            $student->getGroup(),
        );
    }
}

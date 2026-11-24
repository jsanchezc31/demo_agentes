<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Task;

use App\Domain\Task\Exception\InvalidTaskDueDate;
use App\Domain\Task\TaskDueDate;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class TaskDueDateTest extends TestCase
{
    public function test_null_means_no_due_date(): void
    {
        self::assertNull(TaskDueDate::fromString(null));
    }

    public function test_it_parses_a_date_without_time(): void
    {
        $date = TaskDueDate::fromString('2026-12-01');

        self::assertNotNull($date);
        self::assertSame('2026-12-01 00:00:00', $date->format('Y-m-d H:i:s'));
    }

    /**
     * @return iterable<string, array{string}>
     */
    public static function invalidDates(): iterable
    {
        yield 'día inexistente' => ['2026-02-30'];
        yield 'formato europeo' => ['01/12/2026'];
        yield 'con hora' => ['2026-12-01 10:00'];
        yield 'texto' => ['mañana'];
    }

    #[DataProvider('invalidDates')]
    public function test_it_does_not_allow_invalid_dates(string $value): void
    {
        $this->expectException(InvalidTaskDueDate::class);

        TaskDueDate::fromString($value);
    }
}

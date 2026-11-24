<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Task;

use App\Domain\Task\Exception\InvalidTaskId;
use App\Domain\Task\TaskId;
use PHPUnit\Framework\TestCase;

final class TaskIdTest extends TestCase
{
    public function test_it_accepts_a_uuid(): void
    {
        $id = TaskId::fromString('01a20d64-c2f1-75c4-bd5f-73498fb98752');

        self::assertSame('01a20d64-c2f1-75c4-bd5f-73498fb98752', $id->value());
    }

    public function test_it_normalizes_to_lowercase(): void
    {
        $id = TaskId::fromString('01A20D64-C2F1-75C4-BD5F-73498FB98752');

        self::assertSame('01a20d64-c2f1-75c4-bd5f-73498fb98752', $id->value());
    }

    public function test_it_does_not_allow_a_value_that_is_not_a_uuid(): void
    {
        $this->expectException(InvalidTaskId::class);

        TaskId::fromString('no-es-un-uuid');
    }

    public function test_ids_with_same_value_are_equal(): void
    {
        $a = TaskId::fromString('01a20d64-c2f1-75c4-bd5f-73498fb98752');
        $b = TaskId::fromString('01A20D64-C2F1-75C4-BD5F-73498FB98752');

        self::assertTrue($a->equals($b));
    }
}

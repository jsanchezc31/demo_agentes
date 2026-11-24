<?php

declare(strict_types=1);

namespace App\Tests\Unit\Domain\Task;

use App\Domain\Task\Exception\InvalidTaskTitle;
use App\Domain\Task\Exception\InvalidTaskTransition;
use App\Domain\Task\Task;
use App\Domain\Task\TaskPriority;
use App\Domain\Task\TaskStatus;
use App\Tests\Mother\TaskMother;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class TaskTest extends TestCase
{
    public function test_new_task_is_pending(): void
    {
        $task = TaskMother::pending();

        self::assertSame(TaskStatus::Pending, $task->status());
    }

    public function test_new_task_has_same_created_and_updated_dates(): void
    {
        $now = new \DateTimeImmutable('2026-03-01 09:30:00');

        $task = Task::create(TaskMother::randomId(), 'Tarea', null, TaskPriority::Low, null, $now);

        self::assertEquals($now, $task->createdAt());
        self::assertEquals($now, $task->updatedAt());
    }

    public function test_title_is_trimmed(): void
    {
        $task = TaskMother::pending('  Comprar pan  ');

        self::assertSame('Comprar pan', $task->title());
    }

    public function test_it_does_not_allow_empty_title(): void
    {
        $this->expectException(InvalidTaskTitle::class);

        TaskMother::pending('   ');
    }

    public function test_it_does_not_allow_title_longer_than_255_characters(): void
    {
        $this->expectException(InvalidTaskTitle::class);

        TaskMother::pending(str_repeat('a', 256));
    }

    public function test_it_allows_title_of_255_characters(): void
    {
        $task = TaskMother::pending(str_repeat('á', 255));

        self::assertSame(255, mb_strlen($task->title()));
    }

    public function test_title_cannot_be_left_empty_after_editing(): void
    {
        $task = TaskMother::pending();

        $this->expectException(InvalidTaskTitle::class);

        $task->update('', null, TaskPriority::Low, null, new \DateTimeImmutable());
    }

    public function test_update_changes_editable_fields_and_updated_date(): void
    {
        $task = TaskMother::pending();
        $now = new \DateTimeImmutable('2026-02-01 12:00:00');
        $dueDate = new \DateTimeImmutable('2026-02-10');

        $task->update('Nuevo título', 'Detalle', TaskPriority::High, $dueDate, $now);

        self::assertSame('Nuevo título', $task->title());
        self::assertSame('Detalle', $task->description());
        self::assertSame(TaskPriority::High, $task->priority());
        self::assertEquals($dueDate, $task->dueDate());
        self::assertEquals($now, $task->updatedAt());
    }

    public function test_blank_description_is_stored_as_null(): void
    {
        $task = TaskMother::pending();

        $task->update('Tarea', '   ', TaskPriority::Medium, null, new \DateTimeImmutable());

        self::assertNull($task->description());
    }

    public function test_done_task_cannot_go_back_to_pending(): void
    {
        $task = TaskMother::done();

        $this->expectException(InvalidTaskTransition::class);

        $task->changeStatus(TaskStatus::Pending, new \DateTimeImmutable());
    }

    public function test_done_task_can_go_back_to_pending_through_in_progress(): void
    {
        $task = TaskMother::done();

        $task->changeStatus(TaskStatus::InProgress, new \DateTimeImmutable());
        $task->changeStatus(TaskStatus::Pending, new \DateTimeImmutable());

        self::assertSame(TaskStatus::Pending, $task->status());
    }

    /**
     * @return iterable<string, array{TaskStatus, TaskStatus}>
     */
    public static function allowedTransitions(): iterable
    {
        yield 'pending → in_progress' => [TaskStatus::Pending, TaskStatus::InProgress];
        yield 'pending → done' => [TaskStatus::Pending, TaskStatus::Done];
        yield 'in_progress → pending' => [TaskStatus::InProgress, TaskStatus::Pending];
        yield 'in_progress → done' => [TaskStatus::InProgress, TaskStatus::Done];
        yield 'done → in_progress' => [TaskStatus::Done, TaskStatus::InProgress];
    }

    #[DataProvider('allowedTransitions')]
    public function test_allowed_transitions_change_status(TaskStatus $from, TaskStatus $to): void
    {
        $task = match ($from) {
            TaskStatus::Pending => TaskMother::pending(),
            TaskStatus::InProgress => TaskMother::inProgress(),
            TaskStatus::Done => TaskMother::done(),
        };

        $task->changeStatus($to, new \DateTimeImmutable());

        self::assertSame($to, $task->status());
    }

    public function test_changing_status_updates_updated_date(): void
    {
        $task = TaskMother::pending();
        $now = new \DateTimeImmutable('2026-05-05 08:00:00');

        $task->changeStatus(TaskStatus::InProgress, $now);

        self::assertEquals($now, $task->updatedAt());
    }

    public function test_changing_to_the_same_status_does_nothing(): void
    {
        $task = TaskMother::done();
        $updatedAt = $task->updatedAt();

        $task->changeStatus(TaskStatus::Done, new \DateTimeImmutable('2030-01-01'));

        self::assertSame(TaskStatus::Done, $task->status());
        self::assertEquals($updatedAt, $task->updatedAt());
    }
}

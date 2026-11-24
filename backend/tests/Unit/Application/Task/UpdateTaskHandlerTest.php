<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Task;

use App\Application\Task\Update\UpdateTaskCommand;
use App\Application\Task\Update\UpdateTaskHandler;
use App\Domain\Task\Exception\InvalidTaskTitle;
use App\Domain\Task\Exception\TaskNotFound;
use App\Tests\Double\InMemoryTaskRepository;
use App\Tests\Mother\TaskMother;
use PHPUnit\Framework\TestCase;

final class UpdateTaskHandlerTest extends TestCase
{
    public function test_it_updates_editable_fields_without_changing_status(): void
    {
        $tasks = new InMemoryTaskRepository();
        $task = TaskMother::done();
        $tasks->save($task);

        $response = (new UpdateTaskHandler($tasks))(
            new UpdateTaskCommand($task->id()->value(), 'Nuevo título', 'Detalle', 'low', '2026-06-30'),
        );

        self::assertSame('Nuevo título', $response->title);
        self::assertSame('Detalle', $response->description);
        self::assertSame('low', $response->priority);
        self::assertSame('2026-06-30', $response->dueDate);
        self::assertSame('done', $response->status);
    }

    public function test_title_cannot_be_left_empty(): void
    {
        $tasks = new InMemoryTaskRepository();
        $task = TaskMother::pending();
        $tasks->save($task);

        $this->expectException(InvalidTaskTitle::class);

        (new UpdateTaskHandler($tasks))(new UpdateTaskCommand($task->id()->value(), '', null, 'low', null));
    }

    public function test_it_fails_when_the_task_does_not_exist(): void
    {
        $this->expectException(TaskNotFound::class);

        (new UpdateTaskHandler(new InMemoryTaskRepository()))(
            new UpdateTaskCommand(TaskMother::randomId()->value(), 'Título', null, 'low', null),
        );
    }
}

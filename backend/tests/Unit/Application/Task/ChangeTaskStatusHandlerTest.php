<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Task;

use App\Application\Task\ChangeStatus\ChangeTaskStatusCommand;
use App\Application\Task\ChangeStatus\ChangeTaskStatusHandler;
use App\Domain\Task\Exception\InvalidTaskTransition;
use App\Domain\Task\Exception\TaskNotFound;
use App\Domain\Task\TaskStatus;
use App\Tests\Double\InMemoryTaskRepository;
use App\Tests\Mother\TaskMother;
use PHPUnit\Framework\TestCase;

final class ChangeTaskStatusHandlerTest extends TestCase
{
    public function test_it_changes_the_status(): void
    {
        $tasks = new InMemoryTaskRepository();
        $task = TaskMother::pending();
        $tasks->save($task);

        $response = (new ChangeTaskStatusHandler($tasks))(new ChangeTaskStatusCommand($task->id()->value(), 'in_progress'));

        self::assertSame('in_progress', $response->status);
        self::assertSame(TaskStatus::InProgress, $tasks->find($task->id())?->status());
    }

    public function test_done_task_cannot_go_back_to_pending(): void
    {
        $tasks = new InMemoryTaskRepository();
        $task = TaskMother::done();
        $tasks->save($task);

        $this->expectException(InvalidTaskTransition::class);

        (new ChangeTaskStatusHandler($tasks))(new ChangeTaskStatusCommand($task->id()->value(), 'pending'));
    }

    public function test_it_fails_when_the_task_does_not_exist(): void
    {
        $this->expectException(TaskNotFound::class);

        (new ChangeTaskStatusHandler(new InMemoryTaskRepository()))(
            new ChangeTaskStatusCommand(TaskMother::randomId()->value(), 'done'),
        );
    }
}

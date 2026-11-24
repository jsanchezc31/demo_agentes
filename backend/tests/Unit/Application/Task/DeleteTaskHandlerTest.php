<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Task;

use App\Application\Task\Delete\DeleteTaskCommand;
use App\Application\Task\Delete\DeleteTaskHandler;
use App\Domain\Task\Exception\TaskNotFound;
use App\Tests\Double\InMemoryTaskRepository;
use App\Tests\Mother\TaskMother;
use PHPUnit\Framework\TestCase;

final class DeleteTaskHandlerTest extends TestCase
{
    public function test_it_removes_the_task(): void
    {
        $tasks = new InMemoryTaskRepository();
        $task = TaskMother::pending();
        $tasks->save($task);

        (new DeleteTaskHandler($tasks))(new DeleteTaskCommand($task->id()->value()));

        self::assertNull($tasks->find($task->id()));
    }

    public function test_it_fails_when_the_task_does_not_exist(): void
    {
        $this->expectException(TaskNotFound::class);

        (new DeleteTaskHandler(new InMemoryTaskRepository()))(new DeleteTaskCommand(TaskMother::randomId()->value()));
    }
}

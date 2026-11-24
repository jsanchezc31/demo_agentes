<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Task;

use App\Application\Task\Get\GetTaskHandler;
use App\Application\Task\Get\GetTaskQuery;
use App\Domain\Task\Exception\TaskNotFound;
use App\Tests\Double\InMemoryTaskRepository;
use App\Tests\Mother\TaskMother;
use PHPUnit\Framework\TestCase;

final class GetTaskHandlerTest extends TestCase
{
    public function test_it_returns_the_task(): void
    {
        $tasks = new InMemoryTaskRepository();
        $task = TaskMother::pending('Leer el análisis');
        $tasks->save($task);

        $response = (new GetTaskHandler($tasks))(new GetTaskQuery($task->id()->value()));

        self::assertSame($task->id()->value(), $response->id);
        self::assertSame('Leer el análisis', $response->title);
    }

    public function test_it_fails_when_the_task_does_not_exist(): void
    {
        $handler = new GetTaskHandler(new InMemoryTaskRepository());

        $this->expectException(TaskNotFound::class);

        $handler(new GetTaskQuery(TaskMother::randomId()->value()));
    }
}

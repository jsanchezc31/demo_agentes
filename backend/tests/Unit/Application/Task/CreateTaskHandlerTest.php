<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Task;

use App\Application\Task\Create\CreateTaskCommand;
use App\Application\Task\Create\CreateTaskHandler;
use App\Domain\Task\Exception\InvalidTaskDueDate;
use App\Domain\Task\Exception\InvalidTaskTitle;
use App\Domain\Task\TaskId;
use App\Tests\Double\InMemoryTaskRepository;
use PHPUnit\Framework\TestCase;

final class CreateTaskHandlerTest extends TestCase
{
    private InMemoryTaskRepository $tasks;
    private CreateTaskHandler $handler;

    protected function setUp(): void
    {
        $this->tasks = new InMemoryTaskRepository();
        $this->handler = new CreateTaskHandler($this->tasks);
    }

    public function test_it_creates_a_pending_task(): void
    {
        $response = ($this->handler)(new CreateTaskCommand('Preparar la demo', 'Con ejemplos', 'high', '2026-12-01'));

        self::assertSame('Preparar la demo', $response->title);
        self::assertSame('Con ejemplos', $response->description);
        self::assertSame('pending', $response->status);
        self::assertSame('high', $response->priority);
        self::assertSame('2026-12-01', $response->dueDate);
    }

    public function test_it_persists_the_task(): void
    {
        $response = ($this->handler)(new CreateTaskCommand('Preparar la demo', null, 'medium', null));

        self::assertNotNull($this->tasks->find(TaskId::fromString($response->id)));
    }

    public function test_it_does_not_create_a_task_with_empty_title(): void
    {
        $this->expectException(InvalidTaskTitle::class);

        ($this->handler)(new CreateTaskCommand('  ', null, 'medium', null));
    }

    public function test_it_does_not_create_a_task_with_invalid_due_date(): void
    {
        $this->expectException(InvalidTaskDueDate::class);

        ($this->handler)(new CreateTaskCommand('Tarea', null, 'medium', '2026-13-01'));
    }
}

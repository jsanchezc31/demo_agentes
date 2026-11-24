<?php

declare(strict_types=1);

namespace App\Tests\Unit\Application\Task;

use App\Application\Task\List\ListTasksHandler;
use App\Application\Task\List\ListTasksQuery;
use App\Domain\Task\TaskPriority;
use App\Tests\Double\InMemoryTaskRepository;
use App\Tests\Mother\TaskMother;
use PHPUnit\Framework\TestCase;

final class ListTasksHandlerTest extends TestCase
{
    private InMemoryTaskRepository $tasks;
    private ListTasksHandler $handler;

    protected function setUp(): void
    {
        $this->tasks = new InMemoryTaskRepository();
        $this->handler = new ListTasksHandler($this->tasks);
    }

    public function test_it_lists_newest_tasks_first(): void
    {
        $this->tasks->save(TaskMother::pending('Antigua', createdAt: new \DateTimeImmutable('2026-01-01')));
        $this->tasks->save(TaskMother::pending('Reciente', createdAt: new \DateTimeImmutable('2026-01-03')));

        $page = ($this->handler)(new ListTasksQuery(null, null, 1, 20));

        self::assertSame(['Reciente', 'Antigua'], array_map(static fn ($task) => $task->title, $page->items));
        self::assertSame(2, $page->total);
    }

    public function test_it_filters_by_status_and_priority(): void
    {
        $this->tasks->save(TaskMother::pending('Pendiente alta', TaskPriority::High));
        $this->tasks->save(TaskMother::done('Terminada alta', TaskPriority::High));
        $this->tasks->save(TaskMother::done('Terminada baja', TaskPriority::Low));

        $page = ($this->handler)(new ListTasksQuery('done', 'high', 1, 20));

        self::assertCount(1, $page->items);
        self::assertSame('Terminada alta', $page->items[0]->title);
        self::assertSame(1, $page->total);
    }

    public function test_it_paginates(): void
    {
        for ($day = 1; $day <= 5; ++$day) {
            $this->tasks->save(TaskMother::pending("Tarea $day", createdAt: new \DateTimeImmutable("2026-01-0$day")));
        }

        $page = ($this->handler)(new ListTasksQuery(null, null, 2, 2));

        self::assertSame(['Tarea 3', 'Tarea 2'], array_map(static fn ($task) => $task->title, $page->items));
        self::assertSame(2, $page->page);
        self::assertSame(2, $page->limit);
        self::assertSame(5, $page->total);
    }
}

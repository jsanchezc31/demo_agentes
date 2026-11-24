<?php

declare(strict_types=1);

namespace App\Tests\Double;

use App\Domain\Task\Task;
use App\Domain\Task\TaskId;
use App\Domain\Task\TaskPriority;
use App\Domain\Task\TaskRepository;
use App\Domain\Task\TaskStatus;
use App\Tests\Mother\TaskMother;

final class InMemoryTaskRepository implements TaskRepository
{
    /** @var array<string, Task> */
    private array $tasks = [];

    public function nextIdentity(): TaskId
    {
        return TaskMother::randomId();
    }

    public function save(Task $task): void
    {
        $this->tasks[$task->id()->value()] = $task;
    }

    public function remove(Task $task): void
    {
        unset($this->tasks[$task->id()->value()]);
    }

    public function find(TaskId $id): ?Task
    {
        return $this->tasks[$id->value()] ?? null;
    }

    public function search(?TaskStatus $status, ?TaskPriority $priority, int $offset, int $limit): array
    {
        $tasks = $this->filtered($status, $priority);

        usort($tasks, static fn (Task $a, Task $b): int => [$b->createdAt(), $b->id()->value()] <=> [$a->createdAt(), $a->id()->value()]);

        return \array_slice($tasks, $offset, $limit);
    }

    public function count(?TaskStatus $status, ?TaskPriority $priority): int
    {
        return \count($this->filtered($status, $priority));
    }

    /**
     * @return list<Task>
     */
    private function filtered(?TaskStatus $status, ?TaskPriority $priority): array
    {
        return array_values(array_filter(
            $this->tasks,
            static fn (Task $task): bool => (null === $status || $task->status() === $status)
                && (null === $priority || $task->priority() === $priority),
        ));
    }
}

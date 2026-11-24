<?php

declare(strict_types=1);

namespace App\Domain\Task;

interface TaskRepository
{
    public function nextIdentity(): TaskId;

    public function save(Task $task): void;

    public function remove(Task $task): void;

    public function find(TaskId $id): ?Task;

    /**
     * Ordenadas de la más reciente a la más antigua.
     *
     * @return list<Task>
     */
    public function search(?TaskStatus $status, ?TaskPriority $priority, int $offset, int $limit): array;

    public function count(?TaskStatus $status, ?TaskPriority $priority): int;
}

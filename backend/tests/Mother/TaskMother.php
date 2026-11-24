<?php

declare(strict_types=1);

namespace App\Tests\Mother;

use App\Domain\Task\Task;
use App\Domain\Task\TaskId;
use App\Domain\Task\TaskPriority;
use App\Domain\Task\TaskStatus;
use Symfony\Component\Uid\Uuid;

final class TaskMother
{
    public static function randomId(): TaskId
    {
        return TaskId::fromString(Uuid::v7()->toRfc4122());
    }

    public static function pending(
        string $title = 'Tarea de prueba',
        TaskPriority $priority = TaskPriority::Medium,
        ?\DateTimeImmutable $createdAt = null,
        ?TaskId $id = null,
    ): Task {
        return Task::create(
            $id ?? self::randomId(),
            $title,
            null,
            $priority,
            null,
            $createdAt ?? new \DateTimeImmutable('2026-01-01 10:00:00'),
        );
    }

    public static function inProgress(
        string $title = 'Tarea en curso',
        TaskPriority $priority = TaskPriority::Medium,
        ?\DateTimeImmutable $createdAt = null,
    ): Task {
        $task = self::pending($title, $priority, $createdAt);
        $task->changeStatus(TaskStatus::InProgress, $task->createdAt());

        return $task;
    }

    public static function done(
        string $title = 'Tarea terminada',
        TaskPriority $priority = TaskPriority::Medium,
        ?\DateTimeImmutable $createdAt = null,
    ): Task {
        $task = self::inProgress($title, $priority, $createdAt);
        $task->changeStatus(TaskStatus::Done, $task->createdAt());

        return $task;
    }
}

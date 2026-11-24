<?php

declare(strict_types=1);

namespace App\Application\Task;

use App\Domain\Task\Task;
use App\Domain\Task\TaskDueDate;

final readonly class TaskResponse
{
    private function __construct(
        public string $id,
        public string $title,
        public ?string $description,
        public string $status,
        public string $priority,
        public ?string $dueDate,
        public string $createdAt,
        public string $updatedAt,
    ) {
    }

    public static function fromTask(Task $task): self
    {
        return new self(
            $task->id()->value(),
            $task->title(),
            $task->description(),
            $task->status()->value,
            $task->priority()->value,
            $task->dueDate()?->format(TaskDueDate::FORMAT),
            $task->createdAt()->format(\DATE_ATOM),
            $task->updatedAt()->format(\DATE_ATOM),
        );
    }
}

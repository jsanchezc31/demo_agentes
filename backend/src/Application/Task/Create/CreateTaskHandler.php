<?php

declare(strict_types=1);

namespace App\Application\Task\Create;

use App\Application\Task\TaskResponse;
use App\Domain\Task\Task;
use App\Domain\Task\TaskDueDate;
use App\Domain\Task\TaskPriority;
use App\Domain\Task\TaskRepository;

final readonly class CreateTaskHandler
{
    public function __construct(private TaskRepository $tasks)
    {
    }

    public function __invoke(CreateTaskCommand $command): TaskResponse
    {
        $task = Task::create(
            $this->tasks->nextIdentity(),
            $command->title,
            $command->description,
            TaskPriority::from($command->priority),
            TaskDueDate::fromString($command->dueDate),
            new \DateTimeImmutable(),
        );

        $this->tasks->save($task);

        return TaskResponse::fromTask($task);
    }
}

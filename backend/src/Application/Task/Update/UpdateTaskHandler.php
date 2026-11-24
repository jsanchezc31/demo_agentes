<?php

declare(strict_types=1);

namespace App\Application\Task\Update;

use App\Application\Task\TaskResponse;
use App\Domain\Task\Exception\TaskNotFound;
use App\Domain\Task\TaskDueDate;
use App\Domain\Task\TaskId;
use App\Domain\Task\TaskPriority;
use App\Domain\Task\TaskRepository;

final readonly class UpdateTaskHandler
{
    public function __construct(private TaskRepository $tasks)
    {
    }

    public function __invoke(UpdateTaskCommand $command): TaskResponse
    {
        $id = TaskId::fromString($command->id);
        $task = $this->tasks->find($id) ?? throw TaskNotFound::withId($id);

        $task->update(
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

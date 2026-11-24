<?php

declare(strict_types=1);

namespace App\Application\Task\ChangeStatus;

use App\Application\Task\TaskResponse;
use App\Domain\Task\Exception\TaskNotFound;
use App\Domain\Task\TaskId;
use App\Domain\Task\TaskRepository;
use App\Domain\Task\TaskStatus;

final readonly class ChangeTaskStatusHandler
{
    public function __construct(private TaskRepository $tasks)
    {
    }

    public function __invoke(ChangeTaskStatusCommand $command): TaskResponse
    {
        $id = TaskId::fromString($command->id);
        $task = $this->tasks->find($id) ?? throw TaskNotFound::withId($id);

        $task->changeStatus(TaskStatus::from($command->status), new \DateTimeImmutable());

        $this->tasks->save($task);

        return TaskResponse::fromTask($task);
    }
}

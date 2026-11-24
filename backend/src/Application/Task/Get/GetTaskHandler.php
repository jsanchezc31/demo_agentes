<?php

declare(strict_types=1);

namespace App\Application\Task\Get;

use App\Application\Task\TaskResponse;
use App\Domain\Task\Exception\TaskNotFound;
use App\Domain\Task\TaskId;
use App\Domain\Task\TaskRepository;

final readonly class GetTaskHandler
{
    public function __construct(private TaskRepository $tasks)
    {
    }

    public function __invoke(GetTaskQuery $query): TaskResponse
    {
        $id = TaskId::fromString($query->id);
        $task = $this->tasks->find($id) ?? throw TaskNotFound::withId($id);

        return TaskResponse::fromTask($task);
    }
}

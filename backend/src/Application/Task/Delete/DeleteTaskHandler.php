<?php

declare(strict_types=1);

namespace App\Application\Task\Delete;

use App\Domain\Task\Exception\TaskNotFound;
use App\Domain\Task\TaskId;
use App\Domain\Task\TaskRepository;

final readonly class DeleteTaskHandler
{
    public function __construct(private TaskRepository $tasks)
    {
    }

    public function __invoke(DeleteTaskCommand $command): void
    {
        $id = TaskId::fromString($command->id);
        $task = $this->tasks->find($id) ?? throw TaskNotFound::withId($id);

        $this->tasks->remove($task);
    }
}

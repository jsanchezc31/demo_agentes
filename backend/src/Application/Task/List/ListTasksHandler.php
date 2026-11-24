<?php

declare(strict_types=1);

namespace App\Application\Task\List;

use App\Application\Task\TaskPageResponse;
use App\Application\Task\TaskResponse;
use App\Domain\Task\TaskPriority;
use App\Domain\Task\TaskRepository;
use App\Domain\Task\TaskStatus;

final readonly class ListTasksHandler
{
    public function __construct(private TaskRepository $tasks)
    {
    }

    public function __invoke(ListTasksQuery $query): TaskPageResponse
    {
        $status = null !== $query->status ? TaskStatus::from($query->status) : null;
        $priority = null !== $query->priority ? TaskPriority::from($query->priority) : null;

        $tasks = $this->tasks->search($status, $priority, ($query->page - 1) * $query->limit, $query->limit);

        return new TaskPageResponse(
            array_map(TaskResponse::fromTask(...), $tasks),
            $query->page,
            $query->limit,
            $this->tasks->count($status, $priority),
        );
    }
}

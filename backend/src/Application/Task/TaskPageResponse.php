<?php

declare(strict_types=1);

namespace App\Application\Task;

final readonly class TaskPageResponse
{
    /**
     * @param list<TaskResponse> $items
     */
    public function __construct(
        public array $items,
        public int $page,
        public int $limit,
        public int $total,
    ) {
    }
}

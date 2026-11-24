<?php

declare(strict_types=1);

namespace App\Application\Task\Update;

final readonly class UpdateTaskCommand
{
    public function __construct(
        public string $id,
        public string $title,
        public ?string $description,
        public string $priority,
        public ?string $dueDate,
    ) {
    }
}

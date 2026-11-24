<?php

declare(strict_types=1);

namespace App\UI\Http\Request;

use App\Domain\Task\TaskPriority;
use App\Domain\Task\TaskStatus;
use Symfony\Component\Validator\Constraints as Assert;

final class ListTasksRequest
{
    public const int DEFAULT_LIMIT = 20;
    public const int MAX_LIMIT = 100;

    public function __construct(
        #[Assert\Choice(callback: [TaskStatus::class, 'values'], message: 'El estado debe ser uno de: {{ choices }}')]
        public readonly ?string $status = null,
        #[Assert\Choice(callback: [TaskPriority::class, 'values'], message: 'La prioridad debe ser una de: {{ choices }}')]
        public readonly ?string $priority = null,
        #[Assert\Positive(message: 'La página debe ser mayor que 0')]
        public readonly int $page = 1,
        #[Assert\Range(notInRangeMessage: 'El límite debe estar entre {{ min }} y {{ max }}', min: 1, max: self::MAX_LIMIT)]
        public readonly int $limit = self::DEFAULT_LIMIT,
    ) {
    }
}

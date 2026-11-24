<?php

declare(strict_types=1);

namespace App\UI\Http\Request;

use App\Domain\Task\TaskStatus;
use Symfony\Component\Validator\Constraints as Assert;

final class ChangeTaskStatusRequest
{
    public function __construct(
        #[Assert\NotNull(message: 'El estado es obligatorio')]
        #[Assert\Choice(callback: [TaskStatus::class, 'values'], message: 'El estado debe ser uno de: {{ choices }}')]
        public readonly ?string $status = null,
    ) {
    }
}

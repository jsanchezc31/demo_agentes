<?php

declare(strict_types=1);

namespace App\UI\Http\Request;

use App\Domain\Task\TaskPriority;
use App\Domain\Task\TaskTitle;
use Symfony\Component\Validator\Constraints as Assert;

final class UpdateTaskRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'El título es obligatorio', normalizer: 'trim')]
        #[Assert\Length(max: TaskTitle::MAX_LENGTH, maxMessage: 'El título no puede superar {{ limit }} caracteres')]
        public readonly ?string $title = null,
        public readonly ?string $description = null,
        #[Assert\NotNull(message: 'La prioridad es obligatoria')]
        #[Assert\Choice(callback: [TaskPriority::class, 'values'], message: 'La prioridad debe ser una de: {{ choices }}')]
        public readonly ?string $priority = null,
        #[Assert\Date(message: 'La fecha límite debe tener el formato YYYY-MM-DD')]
        public readonly ?string $dueDate = null,
    ) {
    }
}

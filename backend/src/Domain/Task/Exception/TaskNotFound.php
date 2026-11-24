<?php

declare(strict_types=1);

namespace App\Domain\Task\Exception;

use App\Domain\Task\TaskId;

final class TaskNotFound extends \DomainException
{
    public static function withId(TaskId $id): self
    {
        return new self(\sprintf('La tarea "%s" no existe', $id->value()));
    }
}

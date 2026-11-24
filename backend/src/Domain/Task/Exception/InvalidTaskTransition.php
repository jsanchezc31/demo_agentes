<?php

declare(strict_types=1);

namespace App\Domain\Task\Exception;

use App\Domain\Task\TaskStatus;

final class InvalidTaskTransition extends \DomainException
{
    public static function between(TaskStatus $from, TaskStatus $to): self
    {
        return new self(\sprintf(
            'Una tarea en estado "%s" no puede pasar a "%s"',
            $from->value,
            $to->value,
        ));
    }
}

<?php

declare(strict_types=1);

namespace App\Domain\Task\Exception;

final class InvalidTaskDueDate extends \DomainException
{
    public static function fromValue(string $value): self
    {
        return new self(\sprintf('"%s" no es una fecha válida (formato YYYY-MM-DD)', $value));
    }
}

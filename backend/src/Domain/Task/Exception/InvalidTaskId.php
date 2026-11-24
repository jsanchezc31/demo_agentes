<?php

declare(strict_types=1);

namespace App\Domain\Task\Exception;

final class InvalidTaskId extends \DomainException
{
    public static function fromValue(string $value): self
    {
        return new self(\sprintf('"%s" no es un identificador de tarea válido', $value));
    }
}

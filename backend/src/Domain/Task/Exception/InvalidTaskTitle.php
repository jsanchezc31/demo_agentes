<?php

declare(strict_types=1);

namespace App\Domain\Task\Exception;

final class InvalidTaskTitle extends \DomainException
{
    public static function empty(): self
    {
        return new self('El título no puede estar vacío');
    }

    public static function tooLong(int $maxLength): self
    {
        return new self(\sprintf('El título no puede superar %d caracteres', $maxLength));
    }
}

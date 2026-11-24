<?php

declare(strict_types=1);

namespace App\Domain\Task;

use App\Domain\Task\Exception\InvalidTaskDueDate;

final class TaskDueDate
{
    public const string FORMAT = 'Y-m-d';

    public static function fromString(?string $value): ?\DateTimeImmutable
    {
        if (null === $value) {
            return null;
        }

        $date = \DateTimeImmutable::createFromFormat('!'.self::FORMAT, $value);

        if (false === $date || $date->format(self::FORMAT) !== $value) {
            throw InvalidTaskDueDate::fromValue($value);
        }

        return $date;
    }
}

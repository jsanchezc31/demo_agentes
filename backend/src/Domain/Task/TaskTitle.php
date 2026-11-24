<?php

declare(strict_types=1);

namespace App\Domain\Task;

use App\Domain\Task\Exception\InvalidTaskTitle;

final class TaskTitle
{
    public const int MAX_LENGTH = 255;

    public static function assert(string $title): string
    {
        $title = trim($title);

        if ('' === $title) {
            throw InvalidTaskTitle::empty();
        }

        if (mb_strlen($title) > self::MAX_LENGTH) {
            throw InvalidTaskTitle::tooLong(self::MAX_LENGTH);
        }

        return $title;
    }
}

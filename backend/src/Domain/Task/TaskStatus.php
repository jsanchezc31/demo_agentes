<?php

declare(strict_types=1);

namespace App\Domain\Task;

enum TaskStatus: string
{
    case Pending = 'pending';
    case InProgress = 'in_progress';
    case Done = 'done';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    public function canTransitionTo(self $target): bool
    {
        return !(self::Done === $this && self::Pending === $target);
    }
}

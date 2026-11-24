<?php

declare(strict_types=1);

namespace App\Domain\Task;

use App\Domain\Task\Exception\InvalidTaskTransition;

/**
 * No es final: Doctrine extiende la entidad para generar sus proxies (lazy ghost objects).
 */
class Task
{
    private function __construct(
        private readonly TaskId $id,
        private string $title,
        private ?string $description,
        private TaskStatus $status,
        private TaskPriority $priority,
        private ?\DateTimeImmutable $dueDate,
        private readonly \DateTimeImmutable $createdAt,
        private \DateTimeImmutable $updatedAt,
    ) {
    }

    public static function create(
        TaskId $id,
        string $title,
        ?string $description,
        TaskPriority $priority,
        ?\DateTimeImmutable $dueDate,
        \DateTimeImmutable $now,
    ): self {
        return new self(
            $id,
            TaskTitle::assert($title),
            self::normalizeDescription($description),
            TaskStatus::Pending,
            $priority,
            $dueDate,
            $now,
            $now,
        );
    }

    public function update(
        string $title,
        ?string $description,
        TaskPriority $priority,
        ?\DateTimeImmutable $dueDate,
        \DateTimeImmutable $now,
    ): void {
        $this->title = TaskTitle::assert($title);
        $this->description = self::normalizeDescription($description);
        $this->priority = $priority;
        $this->dueDate = $dueDate;
        $this->updatedAt = $now;
    }

    public function changeStatus(TaskStatus $status, \DateTimeImmutable $now): void
    {
        if ($status === $this->status) {
            return;
        }

        if (!$this->status->canTransitionTo($status)) {
            throw InvalidTaskTransition::between($this->status, $status);
        }

        $this->status = $status;
        $this->updatedAt = $now;
    }

    public function id(): TaskId
    {
        return $this->id;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function status(): TaskStatus
    {
        return $this->status;
    }

    public function priority(): TaskPriority
    {
        return $this->priority;
    }

    public function dueDate(): ?\DateTimeImmutable
    {
        return $this->dueDate;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function updatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    private static function normalizeDescription(?string $description): ?string
    {
        if (null === $description || '' === trim($description)) {
            return null;
        }

        return $description;
    }
}

<?php

declare(strict_types=1);

namespace App\UI\Http\EventListener;

final readonly class ApiError
{
    /**
     * @param list<array{field: string, message: string}> $details
     */
    public function __construct(
        public int $status,
        public string $code,
        public string $message,
        public array $details = [],
    ) {
    }
}

<?php

declare(strict_types=1);

namespace App\UI\Http\Controller;

use App\Application\Task\ChangeStatus\ChangeTaskStatusCommand;
use App\Application\Task\ChangeStatus\ChangeTaskStatusHandler;
use App\UI\Http\Request\ChangeTaskStatusRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[AsController]
#[Route('/api/tasks/{id}/status', name: 'api_tasks_change_status', requirements: ['id' => Requirement::UUID], methods: ['PATCH'])]
final readonly class ChangeTaskStatusController
{
    public function __construct(private ChangeTaskStatusHandler $handler)
    {
    }

    public function __invoke(string $id, #[MapRequestPayload] ChangeTaskStatusRequest $request): JsonResponse
    {
        $task = ($this->handler)(new ChangeTaskStatusCommand($id, (string) $request->status));

        return new JsonResponse($task);
    }
}

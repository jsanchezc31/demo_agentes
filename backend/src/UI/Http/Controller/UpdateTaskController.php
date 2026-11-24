<?php

declare(strict_types=1);

namespace App\UI\Http\Controller;

use App\Application\Task\Update\UpdateTaskCommand;
use App\Application\Task\Update\UpdateTaskHandler;
use App\UI\Http\Request\UpdateTaskRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[AsController]
#[Route('/api/tasks/{id}', name: 'api_tasks_update', requirements: ['id' => Requirement::UUID], methods: ['PUT'])]
final readonly class UpdateTaskController
{
    public function __construct(private UpdateTaskHandler $handler)
    {
    }

    public function __invoke(string $id, #[MapRequestPayload] UpdateTaskRequest $request): JsonResponse
    {
        $task = ($this->handler)(new UpdateTaskCommand(
            $id,
            (string) $request->title,
            $request->description,
            (string) $request->priority,
            $request->dueDate,
        ));

        return new JsonResponse($task);
    }
}

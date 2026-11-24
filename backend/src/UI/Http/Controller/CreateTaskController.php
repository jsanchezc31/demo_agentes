<?php

declare(strict_types=1);

namespace App\UI\Http\Controller;

use App\Application\Task\Create\CreateTaskCommand;
use App\Application\Task\Create\CreateTaskHandler;
use App\UI\Http\Request\CreateTaskRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route('/api/tasks', name: 'api_tasks_create', methods: ['POST'])]
final readonly class CreateTaskController
{
    public function __construct(private CreateTaskHandler $handler)
    {
    }

    public function __invoke(#[MapRequestPayload] CreateTaskRequest $request): JsonResponse
    {
        $task = ($this->handler)(new CreateTaskCommand(
            (string) $request->title,
            $request->description,
            (string) $request->priority,
            $request->dueDate,
        ));

        return new JsonResponse($task, Response::HTTP_CREATED);
    }
}

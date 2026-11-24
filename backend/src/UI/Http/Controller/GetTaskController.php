<?php

declare(strict_types=1);

namespace App\UI\Http\Controller;

use App\Application\Task\Get\GetTaskHandler;
use App\Application\Task\Get\GetTaskQuery;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[AsController]
#[Route('/api/tasks/{id}', name: 'api_tasks_get', requirements: ['id' => Requirement::UUID], methods: ['GET'])]
final readonly class GetTaskController
{
    public function __construct(private GetTaskHandler $handler)
    {
    }

    public function __invoke(string $id): JsonResponse
    {
        return new JsonResponse(($this->handler)(new GetTaskQuery($id)));
    }
}

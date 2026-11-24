<?php

declare(strict_types=1);

namespace App\UI\Http\Controller;

use App\Application\Task\List\ListTasksHandler;
use App\Application\Task\List\ListTasksQuery;
use App\UI\Http\Request\ListTasksRequest;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
#[Route('/api/tasks', name: 'api_tasks_list', methods: ['GET'])]
final readonly class ListTasksController
{
    public function __construct(private ListTasksHandler $handler)
    {
    }

    public function __invoke(#[MapQueryString] ListTasksRequest $request = new ListTasksRequest()): JsonResponse
    {
        $page = ($this->handler)(new ListTasksQuery(
            $request->status,
            $request->priority,
            $request->page,
            $request->limit,
        ));

        return new JsonResponse($page);
    }
}

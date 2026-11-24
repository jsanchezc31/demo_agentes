<?php

declare(strict_types=1);

namespace App\UI\Http\Controller;

use App\Application\Task\Delete\DeleteTaskCommand;
use App\Application\Task\Delete\DeleteTaskHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[AsController]
#[Route('/api/tasks/{id}', name: 'api_tasks_delete', requirements: ['id' => Requirement::UUID], methods: ['DELETE'])]
final readonly class DeleteTaskController
{
    public function __construct(private DeleteTaskHandler $handler)
    {
    }

    public function __invoke(string $id): Response
    {
        ($this->handler)(new DeleteTaskCommand($id));

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}

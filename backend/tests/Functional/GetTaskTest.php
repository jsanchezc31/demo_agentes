<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Tests\Mother\TaskMother;

final class GetTaskTest extends ApiTestCase
{
    public function test_it_returns_the_task(): void
    {
        $task = TaskMother::inProgress('Detalle');
        $this->given($task);

        $this->client->request('GET', '/api/tasks/'.$task->id()->value());

        self::assertResponseIsSuccessful();
        self::assertSame([
            'id' => $task->id()->value(),
            'title' => 'Detalle',
            'description' => null,
            'status' => 'in_progress',
            'priority' => 'medium',
            'dueDate' => null,
            'createdAt' => '2026-01-01T10:00:00+00:00',
            'updatedAt' => '2026-01-01T10:00:00+00:00',
        ], $this->responseJson());
    }

    public function test_it_returns_404_when_the_task_does_not_exist(): void
    {
        $this->client->request('GET', '/api/tasks/'.TaskMother::randomId()->value());

        $this->assertError(404, 'task_not_found');
    }

    public function test_it_returns_404_when_the_id_is_not_a_uuid(): void
    {
        $this->client->request('GET', '/api/tasks/no-es-un-uuid');

        $this->assertError(404, 'not_found');
    }
}

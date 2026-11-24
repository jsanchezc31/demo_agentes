<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Tests\Mother\TaskMother;

final class DeleteTaskTest extends ApiTestCase
{
    public function test_it_deletes_the_task(): void
    {
        $task = TaskMother::pending();
        $this->given($task);
        $uri = '/api/tasks/'.$task->id()->value();

        $this->client->request('DELETE', $uri);

        self::assertResponseStatusCodeSame(204);
        self::assertSame('', $this->client->getResponse()->getContent());

        $this->client->request('GET', $uri);

        $this->assertError(404, 'task_not_found');
    }

    public function test_it_returns_404_when_the_task_does_not_exist(): void
    {
        $this->client->request('DELETE', '/api/tasks/'.TaskMother::randomId()->value());

        $this->assertError(404, 'task_not_found');
    }
}

<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Tests\Mother\TaskMother;

final class ChangeTaskStatusTest extends ApiTestCase
{
    public function test_it_changes_the_status(): void
    {
        $task = TaskMother::pending();
        $this->given($task);

        $this->sendJson('PATCH', '/api/tasks/'.$task->id()->value().'/status', ['status' => 'in_progress']);

        self::assertResponseIsSuccessful();
        self::assertSame('in_progress', $this->responseJson()['status']);
    }

    public function test_done_task_cannot_go_back_to_pending(): void
    {
        $task = TaskMother::done();
        $this->given($task);

        $this->sendJson('PATCH', '/api/tasks/'.$task->id()->value().'/status', ['status' => 'pending']);

        $this->assertError(422, 'invalid_task_transition');
    }

    public function test_done_task_can_go_back_to_pending_through_in_progress(): void
    {
        $task = TaskMother::done();
        $this->given($task);
        $uri = '/api/tasks/'.$task->id()->value().'/status';

        $this->sendJson('PATCH', $uri, ['status' => 'in_progress']);
        $this->sendJson('PATCH', $uri, ['status' => 'pending']);

        self::assertResponseIsSuccessful();
        self::assertSame('pending', $this->responseJson()['status']);
    }

    public function test_it_returns_404_when_the_task_does_not_exist(): void
    {
        $this->sendJson('PATCH', '/api/tasks/'.TaskMother::randomId()->value().'/status', ['status' => 'done']);

        $this->assertError(404, 'task_not_found');
    }

    public function test_it_rejects_malformed_json(): void
    {
        $task = TaskMother::pending();
        $this->given($task);

        $this->sendRaw('PATCH', '/api/tasks/'.$task->id()->value().'/status', 'status=done');

        $this->assertError(400, 'invalid_json');
    }

    public function test_it_rejects_an_unknown_status(): void
    {
        $task = TaskMother::pending();
        $this->given($task);

        $this->sendJson('PATCH', '/api/tasks/'.$task->id()->value().'/status', ['status' => 'archived']);

        $this->assertError(422, 'validation_failed');
        self::assertSame(['status'], $this->errorFields());
    }
}

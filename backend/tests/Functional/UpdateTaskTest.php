<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Tests\Mother\TaskMother;

final class UpdateTaskTest extends ApiTestCase
{
    public function test_it_updates_the_task_without_changing_its_status(): void
    {
        $task = TaskMother::done();
        $this->given($task);

        $this->sendJson('PUT', '/api/tasks/'.$task->id()->value(), [
            'title' => 'Título editado',
            'description' => 'Descripción nueva',
            'priority' => 'low',
            'dueDate' => '2026-07-15',
        ]);

        self::assertResponseIsSuccessful();
        $updated = $this->responseJson();
        self::assertSame('Título editado', $updated['title']);
        self::assertSame('Descripción nueva', $updated['description']);
        self::assertSame('low', $updated['priority']);
        self::assertSame('2026-07-15', $updated['dueDate']);
        self::assertSame('done', $updated['status']);
        self::assertNotSame($updated['createdAt'], $updated['updatedAt']);
    }

    public function test_it_returns_404_when_the_task_does_not_exist(): void
    {
        $this->sendJson('PUT', '/api/tasks/'.TaskMother::randomId()->value(), ['title' => 'Título', 'priority' => 'low']);

        $this->assertError(404, 'task_not_found');
    }

    public function test_it_rejects_malformed_json(): void
    {
        $task = TaskMother::pending();
        $this->given($task);

        $this->sendRaw('PUT', '/api/tasks/'.$task->id()->value(), '{');

        $this->assertError(400, 'invalid_json');
    }

    public function test_title_cannot_be_left_empty(): void
    {
        $task = TaskMother::pending();
        $this->given($task);

        $this->sendJson('PUT', '/api/tasks/'.$task->id()->value(), ['title' => '', 'priority' => 'low']);

        $this->assertError(422, 'validation_failed');
        self::assertSame(['title'], $this->errorFields());
    }

    public function test_priority_is_required(): void
    {
        $task = TaskMother::pending();
        $this->given($task);

        $this->sendJson('PUT', '/api/tasks/'.$task->id()->value(), ['title' => 'Título']);

        $this->assertError(422, 'validation_failed');
        self::assertSame(['priority'], $this->errorFields());
    }
}

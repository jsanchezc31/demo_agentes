<?php

declare(strict_types=1);

namespace App\Tests\Functional;

final class CreateTaskTest extends ApiTestCase
{
    public function test_it_creates_a_task(): void
    {
        $this->sendJson('POST', '/api/tasks', [
            'title' => 'Preparar la demo',
            'description' => 'Con ejemplos',
            'priority' => 'high',
            'dueDate' => '2026-12-01',
        ]);

        self::assertResponseStatusCodeSame(201);
        $task = $this->responseJson();
        self::assertIsString($task['id']);
        self::assertSame('Preparar la demo', $task['title']);
        self::assertSame('Con ejemplos', $task['description']);
        self::assertSame('pending', $task['status']);
        self::assertSame('high', $task['priority']);
        self::assertSame('2026-12-01', $task['dueDate']);
        self::assertSame($task['createdAt'], $task['updatedAt']);
    }

    public function test_priority_defaults_to_medium(): void
    {
        $this->sendJson('POST', '/api/tasks', ['title' => 'Sin prioridad']);

        self::assertResponseStatusCodeSame(201);
        self::assertSame('medium', $this->responseJson()['priority']);
    }

    public function test_created_task_can_be_retrieved(): void
    {
        $this->sendJson('POST', '/api/tasks', ['title' => 'Recuperable']);
        $id = $this->responseJson()['id'];
        self::assertIsString($id);

        $this->client->request('GET', "/api/tasks/$id");

        self::assertResponseIsSuccessful();
        self::assertSame('Recuperable', $this->responseJson()['title']);
    }

    public function test_it_rejects_malformed_json(): void
    {
        $this->sendRaw('POST', '/api/tasks', '{"title": ');

        $this->assertError(400, 'invalid_json');
    }

    public function test_it_requires_a_title(): void
    {
        $this->sendJson('POST', '/api/tasks', ['title' => '   ']);

        $this->assertError(422, 'validation_failed');
        self::assertSame(['title'], $this->errorFields());
    }

    public function test_it_rejects_a_title_longer_than_255_characters(): void
    {
        $this->sendJson('POST', '/api/tasks', ['title' => str_repeat('a', 256)]);

        $this->assertError(422, 'validation_failed');
        self::assertSame(['title'], $this->errorFields());
    }

    public function test_it_rejects_unknown_priority_and_invalid_due_date(): void
    {
        $this->sendJson('POST', '/api/tasks', ['title' => 'Tarea', 'priority' => 'urgent', 'dueDate' => '2026-02-30']);

        $this->assertError(422, 'validation_failed');
        self::assertSame(['priority', 'dueDate'], $this->errorFields());
    }
}

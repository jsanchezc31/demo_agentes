<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Domain\Task\TaskPriority;
use App\Tests\Mother\TaskMother;

final class ListTasksTest extends ApiTestCase
{
    public function test_it_lists_tasks_newest_first_with_default_pagination(): void
    {
        $this->given(
            TaskMother::pending('Antigua', createdAt: new \DateTimeImmutable('2026-01-01')),
            TaskMother::pending('Reciente', createdAt: new \DateTimeImmutable('2026-01-02')),
        );

        $this->client->request('GET', '/api/tasks');

        self::assertResponseIsSuccessful();
        $page = $this->responseJson();
        self::assertSame(1, $page['page']);
        self::assertSame(20, $page['limit']);
        self::assertSame(2, $page['total']);
        self::assertSame(['Reciente', 'Antigua'], $this->titles($page));
    }

    public function test_it_returns_an_empty_page_when_there_are_no_tasks(): void
    {
        $this->client->request('GET', '/api/tasks');

        self::assertResponseIsSuccessful();
        self::assertSame([], $this->responseJson()['items']);
        self::assertSame(0, $this->responseJson()['total']);
    }

    public function test_it_filters_by_status_and_priority(): void
    {
        $this->given(
            TaskMother::pending('Pendiente alta', TaskPriority::High),
            TaskMother::done('Terminada alta', TaskPriority::High),
            TaskMother::done('Terminada baja', TaskPriority::Low),
        );

        $this->client->request('GET', '/api/tasks?status=done&priority=high');

        self::assertResponseIsSuccessful();
        self::assertSame(['Terminada alta'], $this->titles($this->responseJson()));
        self::assertSame(1, $this->responseJson()['total']);
    }

    public function test_it_paginates(): void
    {
        for ($day = 1; $day <= 3; ++$day) {
            $this->given(TaskMother::pending("Tarea $day", createdAt: new \DateTimeImmutable("2026-01-0$day")));
        }

        $this->client->request('GET', '/api/tasks?page=2&limit=2');

        self::assertResponseIsSuccessful();
        $page = $this->responseJson();
        self::assertSame(['Tarea 1'], $this->titles($page));
        self::assertSame(2, $page['page']);
        self::assertSame(2, $page['limit']);
        self::assertSame(3, $page['total']);
    }

    public function test_it_rejects_invalid_filters(): void
    {
        $this->client->request('GET', '/api/tasks?status=archived&priority=urgent');

        $this->assertError(422, 'validation_failed');
        self::assertSame(['status', 'priority'], $this->errorFields());
    }

    public function test_it_rejects_invalid_pagination(): void
    {
        $this->client->request('GET', '/api/tasks?page=0&limit=101');

        $this->assertError(422, 'validation_failed');
        self::assertSame(['page', 'limit'], $this->errorFields());
    }

    /**
     * @param array<mixed> $page
     *
     * @return list<mixed>
     */
    private function titles(array $page): array
    {
        self::assertIsArray($page['items']);

        return array_values(array_map(static fn (mixed $task): mixed => \is_array($task) ? $task['title'] : null, $page['items']));
    }
}

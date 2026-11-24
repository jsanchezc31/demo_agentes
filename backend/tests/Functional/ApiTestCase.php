<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Domain\Task\Task;
use App\Domain\Task\TaskRepository;
use App\Tests\Support\RecreatesDatabaseSchema;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

abstract class ApiTestCase extends WebTestCase
{
    use RecreatesDatabaseSchema;

    protected KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->recreateDatabaseSchema(static::getContainer()->get(EntityManagerInterface::class));
    }

    protected function given(Task ...$tasks): void
    {
        $repository = static::getContainer()->get(TaskRepository::class);

        foreach ($tasks as $task) {
            $repository->save($task);
        }
    }

    /**
     * @param array<string, mixed> $body
     */
    protected function sendJson(string $method, string $uri, array $body = []): void
    {
        $this->client->jsonRequest($method, $uri, $body);
    }

    protected function sendRaw(string $method, string $uri, string $body): void
    {
        $this->client->request($method, $uri, server: ['CONTENT_TYPE' => 'application/json'], content: $body);
    }

    /**
     * @return array<mixed>
     */
    protected function responseJson(): array
    {
        $data = json_decode((string) $this->client->getResponse()->getContent(), true, flags: \JSON_THROW_ON_ERROR);
        self::assertIsArray($data);

        return $data;
    }

    protected function assertError(int $status, string $code): void
    {
        self::assertResponseStatusCodeSame($status);
        $error = $this->responseJson()['error'] ?? null;
        self::assertIsArray($error);
        self::assertSame($code, $error['code'] ?? null);
    }

    /**
     * @return list<string>
     */
    protected function errorFields(): array
    {
        $error = $this->responseJson()['error'] ?? null;
        self::assertIsArray($error);
        $details = $error['details'] ?? null;
        self::assertIsArray($details);

        return array_values(array_map(static fn (mixed $detail): string => \is_array($detail) ? (string) ($detail['field'] ?? '') : '', $details));
    }
}

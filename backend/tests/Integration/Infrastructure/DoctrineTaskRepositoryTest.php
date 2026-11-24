<?php

declare(strict_types=1);

namespace App\Tests\Integration\Infrastructure;

use App\Domain\Task\TaskPriority;
use App\Domain\Task\TaskStatus;
use App\Infrastructure\Persistence\Doctrine\Repository\DoctrineTaskRepository;
use App\Tests\Mother\TaskMother;
use App\Tests\Support\RecreatesDatabaseSchema;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class DoctrineTaskRepositoryTest extends KernelTestCase
{
    use RecreatesDatabaseSchema;

    private EntityManagerInterface $em;
    private DoctrineTaskRepository $repository;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->em = self::getContainer()->get(EntityManagerInterface::class);
        $this->repository = self::getContainer()->get(DoctrineTaskRepository::class);
        $this->recreateDatabaseSchema($this->em);
    }

    public function test_next_identity_returns_a_new_id_each_time(): void
    {
        self::assertFalse($this->repository->nextIdentity()->equals($this->repository->nextIdentity()));
    }

    public function test_it_saves_and_finds_a_task(): void
    {
        $task = TaskMother::inProgress('Persistida', TaskPriority::High);

        $this->repository->save($task);
        $this->em->clear();
        $found = $this->repository->find($task->id());

        self::assertNotNull($found);
        self::assertNotSame($task, $found);
        self::assertTrue($task->id()->equals($found->id()));
        self::assertSame('Persistida', $found->title());
        self::assertSame(TaskStatus::InProgress, $found->status());
        self::assertSame(TaskPriority::High, $found->priority());
        self::assertEquals($task->createdAt(), $found->createdAt());
    }

    public function test_it_keeps_the_due_date_without_time(): void
    {
        $task = TaskMother::pending();
        $task->update('Con fecha', null, TaskPriority::Low, new \DateTimeImmutable('2026-12-01'), new \DateTimeImmutable());

        $this->repository->save($task);
        $this->em->clear();

        self::assertSame('2026-12-01', $this->repository->find($task->id())?->dueDate()?->format('Y-m-d'));
    }

    public function test_find_returns_null_when_the_task_does_not_exist(): void
    {
        self::assertNull($this->repository->find(TaskMother::randomId()));
    }

    public function test_it_removes_a_task(): void
    {
        $task = TaskMother::pending();
        $this->repository->save($task);

        $this->repository->remove($task);
        $this->em->clear();

        self::assertNull($this->repository->find($task->id()));
    }

    public function test_search_filters_by_status_and_priority(): void
    {
        $this->repository->save(TaskMother::pending('Pendiente alta', TaskPriority::High));
        $this->repository->save(TaskMother::done('Terminada alta', TaskPriority::High));
        $this->repository->save(TaskMother::done('Terminada baja', TaskPriority::Low));

        $tasks = $this->repository->search(TaskStatus::Done, TaskPriority::High, 0, 20);

        self::assertCount(1, $tasks);
        self::assertSame('Terminada alta', $tasks[0]->title());
        self::assertSame(2, $this->repository->count(TaskStatus::Done, null));
        self::assertSame(3, $this->repository->count(null, null));
    }

    public function test_search_orders_newest_first_and_paginates(): void
    {
        for ($day = 1; $day <= 5; ++$day) {
            $this->repository->save(TaskMother::pending("Tarea $day", createdAt: new \DateTimeImmutable("2026-01-0$day")));
        }

        $tasks = $this->repository->search(null, null, 2, 2);

        self::assertSame(['Tarea 3', 'Tarea 2'], array_map(static fn ($task) => $task->title(), $tasks));
    }
}

<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Domain\Task\Task;
use App\Domain\Task\TaskId;
use App\Domain\Task\TaskPriority;
use App\Domain\Task\TaskRepository;
use App\Domain\Task\TaskStatus;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Uid\Uuid;

final class DoctrineTaskRepository implements TaskRepository
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function nextIdentity(): TaskId
    {
        return TaskId::fromString(Uuid::v7()->toRfc4122());
    }

    public function save(Task $task): void
    {
        $this->em->persist($task);
        $this->em->flush();
    }

    public function remove(Task $task): void
    {
        $this->em->remove($task);
        $this->em->flush();
    }

    public function find(TaskId $id): ?Task
    {
        return $this->em->find(Task::class, $id->value());
    }

    public function search(?TaskStatus $status, ?TaskPriority $priority, int $offset, int $limit): array
    {
        /** @var list<Task> $tasks */
        $tasks = $this->filtered($status, $priority)
            ->orderBy('t.createdAt', 'DESC')
            ->addOrderBy('t.id', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return $tasks;
    }

    public function count(?TaskStatus $status, ?TaskPriority $priority): int
    {
        return (int) $this->filtered($status, $priority)
            ->select('COUNT(t.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    private function filtered(?TaskStatus $status, ?TaskPriority $priority): QueryBuilder
    {
        $qb = $this->em->createQueryBuilder()
            ->select('t')
            ->from(Task::class, 't');

        if (null !== $status) {
            $qb->andWhere('t.status = :status')->setParameter('status', $status->value);
        }

        if (null !== $priority) {
            $qb->andWhere('t.priority = :priority')->setParameter('priority', $priority->value);
        }

        return $qb;
    }
}

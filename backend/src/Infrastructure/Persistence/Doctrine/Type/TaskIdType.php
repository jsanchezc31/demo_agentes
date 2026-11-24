<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Type;

use App\Domain\Task\Exception\InvalidTaskId;
use App\Domain\Task\TaskId;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Exception\InvalidType;
use Doctrine\DBAL\Types\Exception\ValueNotConvertible;
use Doctrine\DBAL\Types\Type;

final class TaskIdType extends Type
{
    public const string NAME = 'task_id';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getGuidTypeDeclarationSQL($column);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?TaskId
    {
        if (null === $value || $value instanceof TaskId) {
            return $value;
        }

        if (!\is_string($value)) {
            throw ValueNotConvertible::new($value, self::NAME);
        }

        try {
            return TaskId::fromString($value);
        } catch (InvalidTaskId $e) {
            throw ValueNotConvertible::new($value, self::NAME, null, $e);
        }
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if (null === $value) {
            return null;
        }

        if ($value instanceof TaskId) {
            return $value->value();
        }

        if (\is_string($value)) {
            try {
                return TaskId::fromString($value)->value();
            } catch (InvalidTaskId $e) {
                throw InvalidType::new($value, self::NAME, ['null', 'string', TaskId::class], $e);
            }
        }

        throw InvalidType::new($value, self::NAME, ['null', 'string', TaskId::class]);
    }
}

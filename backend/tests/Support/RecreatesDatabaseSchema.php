<?php

declare(strict_types=1);

namespace App\Tests\Support;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;

trait RecreatesDatabaseSchema
{
    private function recreateDatabaseSchema(EntityManagerInterface $em): void
    {
        $metadata = $em->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($em);

        $schemaTool->dropSchema($metadata);
        $schemaTool->createSchema($metadata);
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20261124114410 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Crea la tabla tasks con índices por estado, prioridad y fecha de creación';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('CREATE TABLE tasks (title VARCHAR(255) NOT NULL, description CLOB DEFAULT NULL, status VARCHAR(20) NOT NULL, priority VARCHAR(10) NOT NULL, due_date DATE DEFAULT NULL, created_at DATETIME NOT NULL, updated_at DATETIME NOT NULL, id CHAR(36) NOT NULL, PRIMARY KEY (id))');
        $this->addSql('CREATE INDEX idx_tasks_status ON tasks (status)');
        $this->addSql('CREATE INDEX idx_tasks_priority ON tasks (priority)');
        $this->addSql('CREATE INDEX idx_tasks_created_at ON tasks (created_at)');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE tasks');
    }
}

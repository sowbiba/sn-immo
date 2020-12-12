<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200413153442 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create attributes table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
CREATE TABLE IF NOT EXISTS attributes (
    `id` BIGINT(20) UNSIGNED AUTO_INCREMENT NOT NULL,
    `name` VARCHAR(255) NOT NULL,
    `type` VARCHAR(255) NOT NULL,
    `values` LONGTEXT DEFAULT NULL,
    PRIMARY KEY(id)
) ENGINE=INNODB;');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS attributes;');
    }
}

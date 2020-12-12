<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201104204503 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create advertising table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
CREATE TABLE IF NOT EXISTS advertising (
    `id` BIGINT(20) UNSIGNED AUTO_INCREMENT NOT NULL,
    `type` VARCHAR(255) NOT NULL,
    `image` VARCHAR(255) NOT NULL,
    `link` LONGTEXT NOT NULL,
    PRIMARY KEY(id)
) ENGINE=INNODB;');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS advertising;');
    }
}

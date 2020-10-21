<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200413151739 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create properties table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
CREATE TABLE IF NOT EXISTS properties (
    id BIGINT(20) UNSIGNED AUTO_INCREMENT NOT NULL,
    title VARCHAR(255) NOT NULL, 
    address LONGTEXT NOT NULL, 
    city VARCHAR(255) NOT NULL, 
    zipcode VARCHAR(10) DEFAULT NULL, 
    created_at DATETIME NOT NULL, 
    updated_at DATETIME DEFAULT NULL, 
    PRIMARY KEY(id)
) ENGINE=INNODB;');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS properties;');
    }
}

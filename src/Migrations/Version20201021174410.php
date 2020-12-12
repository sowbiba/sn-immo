<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20201021174410 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create property_attachments table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
CREATE TABLE IF NOT EXISTS property_attachments (
    id BIGINT(20) UNSIGNED AUTO_INCREMENT NOT NULL,
    property_id BIGINT(20) UNSIGNED NOT NULL, 
    type VARCHAR(255) NOT NULL, 
    slug VARCHAR(255) NOT NULL, 
    path VARCHAR(255) NOT NULL, 
    display_name VARCHAR(255) DEFAULT NULL, 
    created_at DATETIME NOT NULL, 
    updated_at DATETIME DEFAULT NULL, 
    PRIMARY KEY(id),
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
) ENGINE=INNODB;');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS property_attachments;');
    }
}

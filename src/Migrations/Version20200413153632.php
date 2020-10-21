<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200413153632 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create property_attributes table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
CREATE TABLE IF NOT EXISTS property_attributes (
    id BIGINT(20) UNSIGNED AUTO_INCREMENT NOT NULL,
    property_id BIGINT(20) UNSIGNED NOT NULL, 
    attribute_id BIGINT(20) UNSIGNED NOT NULL, 
    value LONGTEXT NOT NULL, 
    created_at DATETIME NOT NULL, 
    updated_at DATETIME DEFAULT NULL, 
    PRIMARY KEY(id),
    UNIQUE KEY property_attributes_property_attribute(property_id, attribute_id),
    FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
    FOREIGN KEY (attribute_id) REFERENCES attributes(id) ON DELETE CASCADE
) ENGINE=INNODB;');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS property_attributes;');
    }
}

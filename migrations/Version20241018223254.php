<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20241018223254 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE country MODIFY area INT DEFAULT NULL');
        $this->addSql('ALTER TABLE country ADD capital VARCHAR(255) DEFAULT NULL AFTER subregion');
    }

    public function down(Schema $schema): void
    {
        // Revert the changes made in the up() method
        $this->addSql('ALTER TABLE country MODIFY area VARCHAR(255) DEFAULT NULL');
        $this->addSql('ALTER TABLE country DROP COLUMN capital');
    }
}

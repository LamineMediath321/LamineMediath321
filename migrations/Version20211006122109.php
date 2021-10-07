<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211006122109 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE store ADD domaine_id INT DEFAULT NULL, DROP domaine');
        $this->addSql('ALTER TABLE store ADD CONSTRAINT FK_FF5758774272FC9F FOREIGN KEY (domaine_id) REFERENCES sous_categorie (id)');
        $this->addSql('CREATE INDEX IDX_FF5758774272FC9F ON store (domaine_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE store DROP FOREIGN KEY FK_FF5758774272FC9F');
        $this->addSql('DROP INDEX IDX_FF5758774272FC9F ON store');
        $this->addSql('ALTER TABLE store ADD domaine VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP domaine_id');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211001234230 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout de attributs prices et rating dans la table articles';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE articles ADD price DOUBLE PRECISION DEFAULT NULL, ADD rating INT NOT NULL');
        $this->addSql('ALTER TABLE image_articles CHANGE image_name image_name VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE articles DROP price, DROP rating');
        $this->addSql('ALTER TABLE image_articles CHANGE image_name image_name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`');
    }
}

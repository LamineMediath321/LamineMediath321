<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211006104842 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creation de la table store';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE store (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, nom_store VARCHAR(255) NOT NULL, domaine VARCHAR(255) NOT NULL, image_name VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_FF575877A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE store ADD CONSTRAINT FK_FF575877A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE store');
    }
}

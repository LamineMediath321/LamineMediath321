<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211022145346 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'creation de la table ladiaMessages';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ladiaMessages (id INT AUTO_INCREMENT NOT NULL, destinataire_id INT DEFAULT NULL, message LONGTEXT DEFAULT NULL, INDEX IDX_E97BF90EA4F84F6E (destinataire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ladiaMessages ADD CONSTRAINT FK_E97BF90EA4F84F6E FOREIGN KEY (destinataire_id) REFERENCES users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE ladiaMessages');
    }
}

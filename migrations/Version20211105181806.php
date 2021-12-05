<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211105181806 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajout de articles dans la table ladiamessages';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ladiamessages ADD article_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE ladiamessages ADD CONSTRAINT FK_E97BF90E7294869C FOREIGN KEY (article_id) REFERENCES articles (id)');
        $this->addSql('CREATE INDEX IDX_E97BF90E7294869C ON ladiamessages (article_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE ladiaMessages DROP FOREIGN KEY FK_E97BF90E7294869C');
        $this->addSql('DROP INDEX IDX_E97BF90E7294869C ON ladiaMessages');
        $this->addSql('ALTER TABLE ladiaMessages DROP article_id');
    }
}

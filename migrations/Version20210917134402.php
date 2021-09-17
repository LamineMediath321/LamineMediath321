<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210917134402 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Relation entre articles et sous_categorie';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE articles ADD sous_categorie_id INT NOT NULL');
        $this->addSql('ALTER TABLE articles ADD CONSTRAINT FK_BFDD3168365BF48 FOREIGN KEY (sous_categorie_id) REFERENCES sous_categorie (id)');
        $this->addSql('CREATE INDEX IDX_BFDD3168365BF48 ON articles (sous_categorie_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE articles DROP FOREIGN KEY FK_BFDD3168365BF48');
        $this->addSql('DROP INDEX IDX_BFDD3168365BF48 ON articles');
        $this->addSql('ALTER TABLE articles DROP sous_categorie_id');
    }
}

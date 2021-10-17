<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211014171715 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Creation du MATCH AGAINTS dans la table articles';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE FULLTEXT INDEX IDX_BFDD3168F7B505AE6DE44026 ON articles (nom_article, description)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP INDEX IDX_BFDD3168F7B505AE6DE44026 ON articles');
    }
}

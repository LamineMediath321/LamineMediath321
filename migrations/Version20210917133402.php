<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210917133402 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Relation entre articles et image_articles ';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image_articles ADD article_id INT NOT NULL');
        $this->addSql('ALTER TABLE image_articles ADD CONSTRAINT FK_302DBFC07294869C FOREIGN KEY (article_id) REFERENCES articles (id)');
        $this->addSql('CREATE INDEX IDX_302DBFC07294869C ON image_articles (article_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE image_articles DROP FOREIGN KEY FK_302DBFC07294869C');
        $this->addSql('DROP INDEX IDX_302DBFC07294869C ON image_articles');
        $this->addSql('ALTER TABLE image_articles DROP article_id');
    }
}

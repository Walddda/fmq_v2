<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211108175048 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE movie (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, name VARCHAR(255) NOT NULL, release_year INTEGER DEFAULT NULL)');
        $this->addSql('CREATE TABLE quote (id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL, movie_id INTEGER NOT NULL, text VARCHAR(255) NOT NULL, character VARCHAR(255) NOT NULL)');
        $this->addSql('CREATE INDEX IDX_6B71CBF48F93B6FC ON quote (movie_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE movie');
        $this->addSql('DROP TABLE quote');
    }
}

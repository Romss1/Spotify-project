<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230730125456 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE "user" ALTER last_call_to_spotify_api SET DEFAULT \'2000-01-01\'');
        $this->addSql('ALTER TABLE "user" ALTER spotify_client_id SET NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE "user" ALTER spotify_client_id DROP NOT NULL');
        $this->addSql('ALTER TABLE "user" ALTER last_call_to_spotify_api SET DEFAULT \'2000-01-01 00:00:00\'');
    }
}

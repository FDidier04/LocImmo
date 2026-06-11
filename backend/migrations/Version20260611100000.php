<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260611100000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add country to real estate listings';
    }

    public function up(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();

        if ('mysql' === $platform || 'postgresql' === $platform) {
            $this->addSql("ALTER TABLE event ADD country VARCHAR(80) NOT NULL DEFAULT 'Republique du Congo'");
            return;
        }

        $this->abortIf(true, sprintf('Unsupported database platform: %s', $platform));
    }

    public function down(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();

        if ('mysql' === $platform || 'postgresql' === $platform) {
            $this->addSql('ALTER TABLE event DROP COLUMN country');
            return;
        }

        $this->abortIf(true, sprintf('Unsupported database platform: %s', $platform));
    }
}

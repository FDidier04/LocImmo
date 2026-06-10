<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260610123000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add offer type to real estate listings';
    }

    public function up(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();

        if ('mysql' === $platform) {
            $this->addSql("ALTER TABLE event ADD offer_type VARCHAR(20) NOT NULL DEFAULT 'Location'");
            return;
        }

        if ('postgresql' === $platform) {
            $this->addSql("ALTER TABLE event ADD offer_type VARCHAR(20) NOT NULL DEFAULT 'Location'");
            return;
        }

        $this->abortIf(true, sprintf('Unsupported database platform: %s', $platform));
    }

    public function down(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();

        if ('mysql' === $platform || 'postgresql' === $platform) {
            $this->addSql('ALTER TABLE event DROP offer_type');
            return;
        }

        $this->abortIf(true, sprintf('Unsupported database platform: %s', $platform));
    }
}

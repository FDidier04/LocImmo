<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260610121000 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Add real estate listing fields for ImmoPlus';
    }

    public function up(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();

        if ('mysql' === $platform) {
            $this->addSql("ALTER TABLE event ADD city VARCHAR(80) NOT NULL, ADD district VARCHAR(120) NOT NULL, ADD property_type VARCHAR(40) NOT NULL, ADD monthly_rent INT NOT NULL, ADD surface_m2 INT NOT NULL, ADD bedrooms INT NOT NULL, ADD bathrooms INT NOT NULL, ADD image_url VARCHAR(255) DEFAULT NULL");
            return;
        }

        if ('postgresql' === $platform) {
            $this->addSql("ALTER TABLE event ADD city VARCHAR(80) NOT NULL");
            $this->addSql("ALTER TABLE event ADD district VARCHAR(120) NOT NULL");
            $this->addSql("ALTER TABLE event ADD property_type VARCHAR(40) NOT NULL");
            $this->addSql("ALTER TABLE event ADD monthly_rent INT NOT NULL");
            $this->addSql("ALTER TABLE event ADD surface_m2 INT NOT NULL");
            $this->addSql("ALTER TABLE event ADD bedrooms INT NOT NULL");
            $this->addSql("ALTER TABLE event ADD bathrooms INT NOT NULL");
            $this->addSql("ALTER TABLE event ADD image_url VARCHAR(255) DEFAULT NULL");
            return;
        }

        $this->abortIf(true, sprintf('Unsupported database platform: %s', $platform));
    }

    public function down(Schema $schema): void
    {
        $platform = $this->connection->getDatabasePlatform()->getName();

        if ('mysql' === $platform) {
            $this->addSql("ALTER TABLE event DROP city, DROP district, DROP property_type, DROP monthly_rent, DROP surface_m2, DROP bedrooms, DROP bathrooms, DROP image_url");
            return;
        }

        if ('postgresql' === $platform) {
            $this->addSql("ALTER TABLE event DROP city");
            $this->addSql("ALTER TABLE event DROP district");
            $this->addSql("ALTER TABLE event DROP property_type");
            $this->addSql("ALTER TABLE event DROP monthly_rent");
            $this->addSql("ALTER TABLE event DROP surface_m2");
            $this->addSql("ALTER TABLE event DROP bedrooms");
            $this->addSql("ALTER TABLE event DROP bathrooms");
            $this->addSql("ALTER TABLE event DROP image_url");
            return;
        }

        $this->abortIf(true, sprintf('Unsupported database platform: %s', $platform));
    }
}

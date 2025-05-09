<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class VersionManualIsVerifiedOnly extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Empty migration to skip duplicate is_verified column addition';
    }

    public function up(Schema $schema): void
    {
        // Intentionally empty to skip duplicate column addition
    }

    public function down(Schema $schema): void
    {
        // Intentionally empty to skip duplicate column removal
    }
}

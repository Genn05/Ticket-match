<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class VersionManualIsVerifiedUpdate extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Empty migration to skip conflicting changes';
    }

    public function up(Schema $schema): void
    {
        // Intentionally empty to skip conflicting changes
    }

    public function down(Schema $schema): void
    {
        // Intentionally empty to skip conflicting changes
    }
}

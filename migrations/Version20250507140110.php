<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250507140110 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Empty migration to skip conflicting schema changes';
    }

    public function up(Schema $schema): void
    {
        // Intentionally empty to skip conflicting schema changes
    }

    public function down(Schema $schema): void
    {
        // Intentionally empty to skip conflicting schema changes
    }
}

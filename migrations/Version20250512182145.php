<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250512182145 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Check if the column 'reset_token' exists before adding it
        $schemaManager = $this->connection->createSchemaManager();
        $columns = $schemaManager->listTableColumns('user');
        if (!array_key_exists('reset_token', $columns)) {
            $this->addSql("ALTER TABLE user ADD reset_token VARCHAR(255) DEFAULT NULL");
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE user DROP reset_token
        SQL);
    }
}

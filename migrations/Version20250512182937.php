<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250512182937 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE reset_password_request CHANGE token token VARCHAR(64) NOT NULL
        SQL);

        // Check if the index 'UNIQ_7CE748A5F37A13B' exists before creating it
        $schemaManager = $this->connection->createSchemaManager();
        $indexes = $schemaManager->listTableIndexes('reset_password_request');
        $indexExists = false;
        foreach ($indexes as $index) {
            if ($index->getName() === 'UNIQ_7CE748A5F37A13B') {
                $indexExists = true;
                break;
            }
        }
        if (!$indexExists) {
            $this->addSql("CREATE UNIQUE INDEX UNIQ_7CE748A5F37A13B ON reset_password_request (token)");
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_7CE748A5F37A13B ON reset_password_request
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reset_password_request CHANGE token token VARCHAR(255) NOT NULL
        SQL);
    }
}

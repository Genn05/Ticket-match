<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250511213343 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $schemaManager = $this->connection->createSchemaManager();
        $columns = $schemaManager->listTableColumns('reset_password_request');
        if (!isset($columns['user_id'])) {
            $this->addSql("ALTER TABLE reset_password_request ADD user_id INT NOT NULL");
        }

        $this->addSql(<<<'SQL'
            ALTER TABLE reset_password_request CHANGE requested_at requested_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', CHANGE expires_at expires_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);

        $foreignKeys = $schemaManager->listTableForeignKeys('reset_password_request');
        $foreignKeyExists = false;
        foreach ($foreignKeys as $foreignKey) {
            if ($foreignKey->getName() === 'FK_7CE748AA76ED395') {
                $foreignKeyExists = true;
                break;
            }
        }
        if (!$foreignKeyExists) {
            $this->addSql("ALTER TABLE reset_password_request ADD CONSTRAINT FK_7CE748AA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)");
        }

        $indexes = $schemaManager->listTableIndexes('reset_password_request');
        $indexExists = false;
        foreach ($indexes as $index) {
            if ($index->getName() === 'IDX_7CE748AA76ED395') {
                $indexExists = true;
                break;
            }
        }
        if (!$indexExists) {
            $this->addSql("CREATE INDEX IDX_7CE748AA76ED395 ON reset_password_request (user_id)");
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE reset_password_request DROP FOREIGN KEY FK_7CE748AA76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_7CE748AA76ED395 ON reset_password_request
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reset_password_request DROP user_id, CHANGE requested_at requested_at DATETIME NOT NULL, CHANGE expires_at expires_at DATETIME NOT NULL
        SQL);
    }
}

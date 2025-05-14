<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250509200002 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        // Check if the table 'profile' exists before creating it
        if (!$schema->hasTable('profile')) {
            $this->addSql(<<<'SQL'
                CREATE TABLE profile (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, adresse VARCHAR(255) NOT NULL, telephone VARCHAR(255) NOT NULL, ville VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
            SQL);
        }
        $this->addSql(<<<'SQL'
            ALTER TABLE reset_password_request CHANGE requested_at requested_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', CHANGE expires_at expires_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)'
        SQL);

        // Check if the column 'profile_id' exists before adding it
        $schemaManager = $this->connection->createSchemaManager();
        $columns = $schemaManager->listTableColumns('user');
        if (!array_key_exists('profile_id', $columns)) {
            $this->addSql(<<<'SQL'
                ALTER TABLE user ADD profile_id INT DEFAULT NULL
            SQL);
        }

        // Check if the foreign key 'FK_8D93D649CCFA12B8' exists before adding it
        $foreignKeys = $schemaManager->listTableForeignKeys('user');
        $foreignKeyExists = false;
        foreach ($foreignKeys as $foreignKey) {
            if ($foreignKey->getName() === 'FK_8D93D649CCFA12B8') {
                $foreignKeyExists = true;
                break;
            }
        }
        if (!$foreignKeyExists) {
            $this->addSql(<<<'SQL'
                ALTER TABLE user ADD CONSTRAINT FK_8D93D649CCFA12B8 FOREIGN KEY (profile_id) REFERENCES profile (id)
            SQL);
        }

        // Check if the index 'IDX_8D93D649CCFA12B8' exists before creating it
        $indexes = $schemaManager->listTableIndexes('user');
        $indexExists = false;
        foreach ($indexes as $index) {
            if ($index->getName() === 'IDX_8D93D649CCFA12B8') {
                $indexExists = true;
                break;
            }
        }
        if (!$indexExists) {
            $this->addSql(<<<'SQL'
                CREATE INDEX IDX_8D93D649CCFA12B8 ON user (profile_id)
            SQL);
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE user DROP FOREIGN KEY FK_8D93D649CCFA12B8
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE profile
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reset_password_request CHANGE requested_at requested_at DATETIME NOT NULL, CHANGE expires_at expires_at DATETIME NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_8D93D649CCFA12B8 ON user
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE user DROP profile_id
        SQL);
    }
}

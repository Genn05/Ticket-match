<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250511212916 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Check if the table 'reset_password_request' exists before creating it
        $schemaManager = $this->connection->createSchemaManager();
        $tables = $schemaManager->listTables();
        $tableExists = false;
        foreach ($tables as $table) {
            if ($table->getName() === 'reset_password_request') {
                $tableExists = true;
                break;
            }
        }
        if (!$tableExists) {
            $this->addSql("CREATE TABLE reset_password_request (id INT AUTO_INCREMENT NOT NULL, token VARCHAR(255) NOT NULL, requested_at DATETIME NOT NULL, expires_at DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB");
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP TABLE reset_password_request
        SQL);
    }
}

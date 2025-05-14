<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250511103231 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $schemaManager = $this->connection->createSchemaManager();
        $indexes = $schemaManager->listTableIndexes('user');
        $indexExists = false;
        foreach ($indexes as $index) {
            if ($index->getName() === 'uniq_identifier_email') {
                $indexExists = true;
                break;
            }
        }
        if ($indexExists) {
            $this->addSql("DROP INDEX uniq_identifier_email ON user");
        }

        // Check if the index 'UNIQ_8D93D649E7927C74' exists before creating it
        $indexes = $schemaManager->listTableIndexes('user');
        $indexExists = false;
        foreach ($indexes as $index) {
            if ($index->getName() === 'UNIQ_8D93D649E7927C74') {
                $indexExists = true;
                break;
            }
        }
        if (!$indexExists) {
            $this->addSql("CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email)");
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            DROP INDEX uniq_8d93d649e7927c74 ON user
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL ON user (email)
        SQL);
    }
}

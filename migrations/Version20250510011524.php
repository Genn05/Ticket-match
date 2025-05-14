<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250510011524 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $schemaManager = $this->connection->createSchemaManager();
        $columns = $schemaManager->listTableColumns('paiement');

        if (!array_key_exists('reservation_id', $columns)) {
            $this->addSql("ALTER TABLE paiement ADD reservation_id INT DEFAULT NULL");
        }

        // Check if the foreign key 'FK_B1DC7A1EB83297E7' exists before adding it
        $foreignKeys = $schemaManager->listTableForeignKeys('paiement');
        $foreignKeyExists = false;
        foreach ($foreignKeys as $foreignKey) {
            if ($foreignKey->getName() === 'FK_B1DC7A1EB83297E7') {
                $foreignKeyExists = true;
                break;
            }
        }
        if (!$foreignKeyExists) {
            $this->addSql("ALTER TABLE paiement ADD CONSTRAINT FK_B1DC7A1EB83297E7 FOREIGN KEY (reservation_id) REFERENCES reservation (id)");
        }

        // Check if the index 'IDX_B1DC7A1EB83297E7' exists before creating it
        $indexes = $schemaManager->listTableIndexes('paiement');
        $indexExists = false;
        foreach ($indexes as $index) {
            if ($index->getName() === 'IDX_B1DC7A1EB83297E7') {
                $indexExists = true;
                break;
            }
        }
        if (!$indexExists) {
            $this->addSql("CREATE INDEX IDX_B1DC7A1EB83297E7 ON paiement (reservation_id)");
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE paiement DROP FOREIGN KEY FK_B1DC7A1EB83297E7
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_B1DC7A1EB83297E7 ON paiement
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE paiement DROP reservation_id
        SQL);
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250509210623 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // Check if the columns 'image_equipe_exterieur' and 'image_equipe_domicile' exist before adding them
        $schemaManager = $this->connection->createSchemaManager();
        $columns = $schemaManager->listTableColumns('mattch');

        if (!array_key_exists('image_equipe_exterieur', $columns)) {
            $this->addSql("ALTER TABLE mattch ADD image_equipe_exterieur LONGTEXT DEFAULT NULL");
        }

        if (!array_key_exists('image_equipe_domicile', $columns)) {
            $this->addSql("ALTER TABLE mattch ADD image_equipe_domicile LONGTEXT DEFAULT NULL");
        }
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE mattch DROP image_equipe_exterieur, DROP image_equipe_domicile
        SQL);
    }
}

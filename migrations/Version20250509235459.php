<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250509235459 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE mattch CHANGE image_equipe_exterieur image_equipe_exterieur VARCHAR(255) DEFAULT NULL, CHANGE image_equipe_domicile image_equipe_domicile VARCHAR(255) DEFAULT NULL
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE mattch CHANGE image_equipe_exterieur image_equipe_exterieur LONGTEXT DEFAULT NULL, CHANGE image_equipe_domicile image_equipe_domicile LONGTEXT DEFAULT NULL
        SQL);
    }
}

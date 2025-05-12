<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250510021934 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE card (id INT AUTO_INCREMENT NOT NULL, card_number VARCHAR(19) NOT NULL, card_type VARCHAR(50) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE paiement ADD card_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE paiement ADD CONSTRAINT FK_B1DC7A1E4ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_B1DC7A1E4ACC9A20 ON paiement (card_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE paiement DROP FOREIGN KEY FK_B1DC7A1E4ACC9A20
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE card
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_B1DC7A1E4ACC9A20 ON paiement
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE paiement DROP card_id
        SQL);
    }
}

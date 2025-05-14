<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250513175303 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE card ADD user_id INT DEFAULT NULL, ADD balance NUMERIC(10, 2) NOT NULL, CHANGE card_number card_number VARCHAR(255) NOT NULL, CHANGE card_type card_type VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE card ADD CONSTRAINT FK_161498D3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE UNIQUE INDEX UNIQ_161498D3E4AF4C20 ON card (card_number)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_161498D3A76ED395 ON card (user_id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE paiement ADD card_number VARCHAR(255) DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD card_id INT DEFAULT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation ADD CONSTRAINT FK_42C849554ACC9A20 FOREIGN KEY (card_id) REFERENCES card (id)
        SQL);
        $this->addSql(<<<'SQL'
            CREATE INDEX IDX_42C849554ACC9A20 ON reservation (card_id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE card DROP FOREIGN KEY FK_161498D3A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX UNIQ_161498D3E4AF4C20 ON card
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_161498D3A76ED395 ON card
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE card DROP user_id, DROP balance, CHANGE card_number card_number VARCHAR(19) NOT NULL, CHANGE card_type card_type VARCHAR(50) NOT NULL
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE paiement DROP card_number
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation DROP FOREIGN KEY FK_42C849554ACC9A20
        SQL);
        $this->addSql(<<<'SQL'
            DROP INDEX IDX_42C849554ACC9A20 ON reservation
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE reservation DROP card_id
        SQL);
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190221202536 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE children CHANGE birthdate birthdate DATETIME DEFAULT NULL, CHANGE body body JSON NOT NULL, CHANGE collected collected NUMERIC(10, 2) DEFAULT NULL, CHANGE goal goal NUMERIC(10, 2) DEFAULT NULL, CHANGE deleted_at deleted_at DATETIME DEFAULT NULL');
        $this->addSql('ALTER TABLE payment_requests CHANGE user_id user_id INT UNSIGNED DEFAULT NULL, CHANGE changed_at changed_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE users CHANGE referrer_id referrer_id INT UNSIGNED DEFAULT NULL, CHANGE roles roles JSON NOT NULL, CHANGE pass pass VARCHAR(100) DEFAULT NULL, CHANGE ref_code ref_code VARCHAR(6) DEFAULT NULL, CHANGE meta meta JSON NOT NULL, CHANGE deleted_at deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE children CHANGE birthdate birthdate DATETIME DEFAULT \'NULL\', CHANGE body body LONGTEXT NOT NULL COLLATE utf8mb4_bin, CHANGE collected collected NUMERIC(10, 2) DEFAULT \'NULL\', CHANGE goal goal NUMERIC(10, 2) DEFAULT \'NULL\', CHANGE deleted_at deleted_at DATETIME DEFAULT \'NULL\'');
        $this->addSql('ALTER TABLE payment_requests CHANGE user_id user_id INT UNSIGNED DEFAULT NULL, CHANGE changed_at changed_at DATETIME DEFAULT \'NULL\' COMMENT \'(DC2Type:datetime_immutable)\'');
        $this->addSql('ALTER TABLE users CHANGE referrer_id referrer_id INT UNSIGNED DEFAULT NULL, CHANGE roles roles LONGTEXT NOT NULL COLLATE utf8mb4_bin, CHANGE pass pass VARCHAR(100) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE ref_code ref_code VARCHAR(6) DEFAULT \'NULL\' COLLATE utf8mb4_unicode_ci, CHANGE meta meta LONGTEXT NOT NULL COLLATE utf8mb4_bin, CHANGE deleted_at deleted_at DATETIME DEFAULT \'NULL\' COMMENT \'(DC2Type:datetime_immutable)\'');
    }
}

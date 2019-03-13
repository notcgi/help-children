<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20190313195311 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE children (id INT UNSIGNED AUTO_INCREMENT NOT NULL, name VARCHAR(120) NOT NULL, birthdate DATETIME DEFAULT NULL, body JSON NOT NULL, collected NUMERIC(10, 2) DEFAULT NULL, goal NUMERIC(10, 2) DEFAULT NULL, deleted_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE child_history (id INT UNSIGNED AUTO_INCREMENT NOT NULL, child_id INT UNSIGNED NOT NULL, donator_id INT UNSIGNED NOT NULL, sum NUMERIC(10, 2) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_450E3850DD62C21B (child_id), INDEX IDX_450E3850831BACAF (donator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE recurring_payments (id INT UNSIGNED AUTO_INCREMENT NOT NULL, user_id INT UNSIGNED NOT NULL, withdrawal_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_F16E5623A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE referral_history (id INT UNSIGNED AUTO_INCREMENT NOT NULL, request_id INT UNSIGNED NOT NULL, user_id INT UNSIGNED NOT NULL, donator_id INT UNSIGNED NOT NULL, sum NUMERIC(10, 2) NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_7E9A2C6D427EB8A5 (request_id), INDEX IDX_7E9A2C6DA76ED395 (user_id), INDEX IDX_7E9A2C6D831BACAF (donator_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE requests (id INT UNSIGNED AUTO_INCREMENT NOT NULL, child_id INT UNSIGNED DEFAULT NULL, user_id INT UNSIGNED NOT NULL, sum NUMERIC(10, 2) NOT NULL, status SMALLINT UNSIGNED NOT NULL, recurent TINYINT(1) NOT NULL, updated_at DATETIME DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_7B85D651DD62C21B (child_id), INDEX IDX_7B85D651A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('CREATE TABLE users (id INT UNSIGNED AUTO_INCREMENT NOT NULL, referrer_id INT UNSIGNED DEFAULT NULL, roles JSON NOT NULL, email VARCHAR(180) NOT NULL, pass VARCHAR(100) DEFAULT NULL, ref_code VARCHAR(6) DEFAULT NULL, meta JSON NOT NULL, reward_sum NUMERIC(10, 2) NOT NULL, deleted_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_1483A5E9E7927C74 (email), INDEX IDX_1483A5E9798C22DB (referrer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE = InnoDB');
        $this->addSql('ALTER TABLE child_history ADD CONSTRAINT FK_450E3850DD62C21B FOREIGN KEY (child_id) REFERENCES children (id)');
        $this->addSql('ALTER TABLE child_history ADD CONSTRAINT FK_450E3850831BACAF FOREIGN KEY (donator_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE recurring_payments ADD CONSTRAINT FK_F16E5623BF396750 FOREIGN KEY (id) REFERENCES requests (id)');
        $this->addSql('ALTER TABLE recurring_payments ADD CONSTRAINT FK_F16E5623A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE referral_history ADD CONSTRAINT FK_7E9A2C6D427EB8A5 FOREIGN KEY (request_id) REFERENCES requests (id)');
        $this->addSql('ALTER TABLE referral_history ADD CONSTRAINT FK_7E9A2C6DA76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE referral_history ADD CONSTRAINT FK_7E9A2C6D831BACAF FOREIGN KEY (donator_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE requests ADD CONSTRAINT FK_7B85D651DD62C21B FOREIGN KEY (child_id) REFERENCES children (id)');
        $this->addSql('ALTER TABLE requests ADD CONSTRAINT FK_7B85D651A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)');
        $this->addSql('ALTER TABLE users ADD CONSTRAINT FK_1483A5E9798C22DB FOREIGN KEY (referrer_id) REFERENCES users (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE child_history DROP FOREIGN KEY FK_450E3850DD62C21B');
        $this->addSql('ALTER TABLE requests DROP FOREIGN KEY FK_7B85D651DD62C21B');
        $this->addSql('ALTER TABLE recurring_payments DROP FOREIGN KEY FK_F16E5623BF396750');
        $this->addSql('ALTER TABLE referral_history DROP FOREIGN KEY FK_7E9A2C6D427EB8A5');
        $this->addSql('ALTER TABLE child_history DROP FOREIGN KEY FK_450E3850831BACAF');
        $this->addSql('ALTER TABLE recurring_payments DROP FOREIGN KEY FK_F16E5623A76ED395');
        $this->addSql('ALTER TABLE referral_history DROP FOREIGN KEY FK_7E9A2C6DA76ED395');
        $this->addSql('ALTER TABLE referral_history DROP FOREIGN KEY FK_7E9A2C6D831BACAF');
        $this->addSql('ALTER TABLE requests DROP FOREIGN KEY FK_7B85D651A76ED395');
        $this->addSql('ALTER TABLE users DROP FOREIGN KEY FK_1483A5E9798C22DB');
        $this->addSql('DROP TABLE children');
        $this->addSql('DROP TABLE child_history');
        $this->addSql('DROP TABLE recurring_payments');
        $this->addSql('DROP TABLE referral_history');
        $this->addSql('DROP TABLE requests');
        $this->addSql('DROP TABLE users');
    }
}

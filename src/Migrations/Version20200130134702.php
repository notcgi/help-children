<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200130134702 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE ch_target ADD name VARCHAR(255) DEFAULT NULL, ADD descr VARCHAR(65500) DEFAULT NULL, ADD attach VARCHAR(65500) DEFAULT NULL');
        // php bin/console doctrine:query:sql "ALTER TABLE ch_target ADD name VARCHAR(255) DEFAULT NULL, ADD descr TEXT(65500) DEFAULT NULL, ADD attach TEXT(65500) DEFAULT NULL"
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() !== 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE children_requests (child VARCHAR(50) NOT NULL COLLATE latin1_swedish_ci, request VARCHAR(50) NOT NULL COLLATE latin1_swedish_ci, sum DOUBLE PRECISION NOT NULL) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE ch_target DROP name, DROP descr, DROP attach');
    }
}

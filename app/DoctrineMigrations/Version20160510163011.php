<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160510163011 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('CREATE TABLE `show` (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, createdOn DATETIME NOT NULL, updatedOn DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB');
        $this->addSql('INSERT INTO `show` (name, createdOn, updatedOn) VALUES ("default", NOW(), NOW())');
        $this->addSql('ALTER TABLE show_order ADD show_order_id INT DEFAULT NULL');
        $this->addSql('UPDATE show_order SET show_order_id = 1');
        $this->addSql('ALTER TABLE show_order ADD CONSTRAINT FK_F798F744B4FB8CD7 FOREIGN KEY (show_order_id) REFERENCES `show` (id)');
        $this->addSql('CREATE INDEX IDX_F798F744B4FB8CD7 ON show_order (show_order_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE show_order DROP FOREIGN KEY FK_F798F744B4FB8CD7');
        $this->addSql('DROP TABLE `show`');
        $this->addSql('DROP INDEX IDX_F798F744B4FB8CD7 ON show_order');
        $this->addSql('ALTER TABLE show_order DROP show_order_id');
    }
}

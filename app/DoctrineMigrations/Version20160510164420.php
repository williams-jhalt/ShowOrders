<?php

namespace Application\Migrations;

use Doctrine\DBAL\Migrations\AbstractMigration;
use Doctrine\DBAL\Schema\Schema;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
class Version20160510164420 extends AbstractMigration
{
    /**
     * @param Schema $schema
     */
    public function up(Schema $schema)
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE show_order DROP FOREIGN KEY FK_F798F744B4FB8CD7');
        $this->addSql('DROP INDEX IDX_F798F744B4FB8CD7 ON show_order');
        $this->addSql('ALTER TABLE show_order CHANGE show_order_id show_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE show_order ADD CONSTRAINT FK_F798F744D0C1FC64 FOREIGN KEY (show_id) REFERENCES `show` (id)');
        $this->addSql('CREATE INDEX IDX_F798F744D0C1FC64 ON show_order (show_id)');
    }

    /**
     * @param Schema $schema
     */
    public function down(Schema $schema)
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->abortIf($this->connection->getDatabasePlatform()->getName() != 'mysql', 'Migration can only be executed safely on \'mysql\'.');

        $this->addSql('ALTER TABLE show_order DROP FOREIGN KEY FK_F798F744D0C1FC64');
        $this->addSql('DROP INDEX IDX_F798F744D0C1FC64 ON show_order');
        $this->addSql('ALTER TABLE show_order CHANGE show_id show_order_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE show_order ADD CONSTRAINT FK_F798F744B4FB8CD7 FOREIGN KEY (show_order_id) REFERENCES `show` (id)');
        $this->addSql('CREATE INDEX IDX_F798F744B4FB8CD7 ON show_order (show_order_id)');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210804144244 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE report DROP FOREIGN KEY FK_C42F7784CFFE9AD6');
        $this->addSql('DROP INDEX IDX_C42F7784CFFE9AD6 ON report');
        $this->addSql('ALTER TABLE report ADD subject VARCHAR(255) NOT NULL, DROP orders_id');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE report ADD orders_id INT DEFAULT NULL, DROP subject');
        $this->addSql('ALTER TABLE report ADD CONSTRAINT FK_C42F7784CFFE9AD6 FOREIGN KEY (orders_id) REFERENCES orders (id) ON UPDATE NO ACTION ON DELETE NO ACTION');
        $this->addSql('CREATE INDEX IDX_C42F7784CFFE9AD6 ON report (orders_id)');
    }
}

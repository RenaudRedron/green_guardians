<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240611100244 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reporting ADD comment_id INT DEFAULT NULL, DROP updated_at');
        $this->addSql('ALTER TABLE reporting ADD CONSTRAINT FK_BD7CFA9FF8697D13 FOREIGN KEY (comment_id) REFERENCES comment (id)');
        $this->addSql('CREATE INDEX IDX_BD7CFA9FF8697D13 ON reporting (comment_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reporting DROP FOREIGN KEY FK_BD7CFA9FF8697D13');
        $this->addSql('DROP INDEX IDX_BD7CFA9FF8697D13 ON reporting');
        $this->addSql('ALTER TABLE reporting ADD updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', DROP comment_id');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240611100341 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE reporting (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, project_id INT DEFAULT NULL, comment_id INT DEFAULT NULL, type INT NOT NULL, content LONGTEXT NOT NULL, created_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_BD7CFA9FA76ED395 (user_id), INDEX IDX_BD7CFA9F166D1F9C (project_id), INDEX IDX_BD7CFA9FF8697D13 (comment_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE reporting ADD CONSTRAINT FK_BD7CFA9FA76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE reporting ADD CONSTRAINT FK_BD7CFA9F166D1F9C FOREIGN KEY (project_id) REFERENCES project (id)');
        $this->addSql('ALTER TABLE reporting ADD CONSTRAINT FK_BD7CFA9FF8697D13 FOREIGN KEY (comment_id) REFERENCES comment (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE reporting DROP FOREIGN KEY FK_BD7CFA9FA76ED395');
        $this->addSql('ALTER TABLE reporting DROP FOREIGN KEY FK_BD7CFA9F166D1F9C');
        $this->addSql('ALTER TABLE reporting DROP FOREIGN KEY FK_BD7CFA9FF8697D13');
        $this->addSql('DROP TABLE reporting');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250720134622 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE restaurant_suggestion (id INT AUTO_INCREMENT NOT NULL, restaurant_id INT DEFAULT NULL, fields JSON NOT NULL COMMENT \'(DC2Type:json)\', INDEX IDX_78BAD32AB1E7706E (restaurant_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE restaurant_suggestion ADD CONSTRAINT FK_78BAD32AB1E7706E FOREIGN KEY (restaurant_id) REFERENCES restaurant (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE restaurant_suggestion DROP FOREIGN KEY FK_78BAD32AB1E7706E');
        $this->addSql('DROP TABLE restaurant_suggestion');
    }
}

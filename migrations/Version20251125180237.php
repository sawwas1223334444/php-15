<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251125180237 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE client DROP phone');
        $this->addSql('ALTER TABLE `order` CHANGE created_at createdAt DATETIME NOT NULL');
        $this->addSql('ALTER TABLE `order` RENAME INDEX idx_f529939819eb6921 TO IDX_34E8BC9C19EB6921');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Client ADD phone VARCHAR(20) DEFAULT NULL');
        $this->addSql('ALTER TABLE `Order` CHANGE createdAt created_at DATETIME NOT NULL');
        $this->addSql('ALTER TABLE `Order` RENAME INDEX idx_34e8bc9c19eb6921 TO IDX_F529939819EB6921');
    }
}

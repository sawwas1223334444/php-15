<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20251211193430 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE OrderFile (id INT AUTO_INCREMENT NOT NULL, filename VARCHAR(255) NOT NULL, originalName VARCHAR(255) NOT NULL, mimeType VARCHAR(100) NOT NULL, size INT NOT NULL, createdAt DATETIME NOT NULL, orderRelation_id INT NOT NULL, INDEX IDX_A06C4D17645B13A1 (orderRelation_id), PRIMARY KEY (id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE OrderFile ADD CONSTRAINT FK_A06C4D17645B13A1 FOREIGN KEY (orderRelation_id) REFERENCES `Order` (id)');
        $this->addSql('ALTER TABLE dish ADD imageName VARCHAR(255) DEFAULT NULL, ADD updatedAt DATETIME DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE OrderFile DROP FOREIGN KEY FK_A06C4D17645B13A1');
        $this->addSql('DROP TABLE OrderFile');
        $this->addSql('ALTER TABLE Dish DROP imageName, DROP updatedAt');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260328111828 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dish DROP FOREIGN KEY FK_957D8CB812469DE2');
        $this->addSql('ALTER TABLE dish DROP FOREIGN KEY FK_957D8CB87E9E4C8C');
        $this->addSql('ALTER TABLE dish CHANGE price price NUMERIC(8, 2) NOT NULL');
        $this->addSql('ALTER TABLE dish ADD CONSTRAINT FK_957D8CB812469DE2 FOREIGN KEY (category_id) REFERENCES category (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE dish ADD CONSTRAINT FK_957D8CB87E9E4C8C FOREIGN KEY (photo_id) REFERENCES photo (id) ON DELETE SET NULL');
        $this->addSql('ALTER TABLE menu DROP FOREIGN KEY FK_7D053A937E9E4C8C');
        $this->addSql('ALTER TABLE menu CHANGE price price NUMERIC(8, 2) NOT NULL');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A937E9E4C8C FOREIGN KEY (photo_id) REFERENCES photo (id) ON DELETE SET NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE dish DROP FOREIGN KEY FK_957D8CB87E9E4C8C');
        $this->addSql('ALTER TABLE dish DROP FOREIGN KEY FK_957D8CB812469DE2');
        $this->addSql('ALTER TABLE dish CHANGE price price DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE dish ADD CONSTRAINT FK_957D8CB87E9E4C8C FOREIGN KEY (photo_id) REFERENCES photo (id)');
        $this->addSql('ALTER TABLE dish ADD CONSTRAINT FK_957D8CB812469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE menu DROP FOREIGN KEY FK_7D053A937E9E4C8C');
        $this->addSql('ALTER TABLE menu CHANGE price price DOUBLE PRECISION NOT NULL');
        $this->addSql('ALTER TABLE menu ADD CONSTRAINT FK_7D053A937E9E4C8C FOREIGN KEY (photo_id) REFERENCES photo (id)');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230519085338 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE alcohol (id UUID NOT NULL, producer_id UUID NOT NULL, image_id UUID DEFAULT NULL, name VARCHAR(255) NOT NULL, type VARCHAR(255) NOT NULL, description VARCHAR(255) DEFAULT NULL, abv DOUBLE PRECISION NOT NULL, PRIMARY KEY(id))');
        $this->addSql('CREATE INDEX IDX_92E97D4589B658FE ON alcohol (producer_id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_92E97D453DA5256D ON alcohol (image_id)');
        $this->addSql('COMMENT ON COLUMN alcohol.id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN alcohol.producer_id IS \'(DC2Type:uuid)\'');
        $this->addSql('COMMENT ON COLUMN alcohol.image_id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE image (id UUID NOT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN image.id IS \'(DC2Type:uuid)\'');
        $this->addSql('CREATE TABLE producer (id UUID NOT NULL, name VARCHAR(255) NOT NULL, country VARCHAR(255) NOT NULL, PRIMARY KEY(id))');
        $this->addSql('COMMENT ON COLUMN producer.id IS \'(DC2Type:uuid)\'');
        $this->addSql('ALTER TABLE alcohol ADD CONSTRAINT FK_92E97D4589B658FE FOREIGN KEY (producer_id) REFERENCES producer (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
        $this->addSql('ALTER TABLE alcohol ADD CONSTRAINT FK_92E97D453DA5256D FOREIGN KEY (image_id) REFERENCES image (id) NOT DEFERRABLE INITIALLY IMMEDIATE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE SCHEMA public');
        $this->addSql('ALTER TABLE alcohol DROP CONSTRAINT FK_92E97D4589B658FE');
        $this->addSql('ALTER TABLE alcohol DROP CONSTRAINT FK_92E97D453DA5256D');
        $this->addSql('DROP TABLE alcohol');
        $this->addSql('DROP TABLE image');
        $this->addSql('DROP TABLE producer');
    }
}

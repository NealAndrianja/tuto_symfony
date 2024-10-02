<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241001135941 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE delivery_mode (id INT AUTO_INCREMENT NOT NULL, mode VARCHAR(255) DEFAULT NULL, fee INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE small_package (id INT AUTO_INCREMENT NOT NULL, customer_id INT DEFAULT NULL, delivery_mode_id INT DEFAULT NULL, tracking_code VARCHAR(255) NOT NULL, reception_date DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', status VARCHAR(255) NOT NULL, weight DOUBLE PRECISION DEFAULT NULL, dimensions LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', volume DOUBLE PRECISION DEFAULT NULL, INDEX IDX_9163BD199395C3F3 (customer_id), INDEX IDX_9163BD197DFB3A94 (delivery_mode_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE small_package ADD CONSTRAINT FK_9163BD199395C3F3 FOREIGN KEY (customer_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE small_package ADD CONSTRAINT FK_9163BD197DFB3A94 FOREIGN KEY (delivery_mode_id) REFERENCES delivery_mode (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE small_package DROP FOREIGN KEY FK_9163BD199395C3F3');
        $this->addSql('ALTER TABLE small_package DROP FOREIGN KEY FK_9163BD197DFB3A94');
        $this->addSql('DROP TABLE delivery_mode');
        $this->addSql('DROP TABLE small_package');
    }
}

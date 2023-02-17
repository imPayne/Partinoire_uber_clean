<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230217195811 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE cleaner (id INT NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE customer (id INT NOT NULL, adresse VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE housework (id INT AUTO_INCREMENT NOT NULL, customer_id INT NOT NULL, date_start DATE NOT NULL, INDEX IDX_18474A479395C3F3 (customer_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE participant (id INT AUTO_INCREMENT NOT NULL, housework_id INT DEFAULT NULL, service_id INT DEFAULT NULL, cleaner_id INT DEFAULT NULL, INDEX IDX_D79F6B11F1BA2DD1 (housework_id), INDEX IDX_D79F6B11ED5CA9E6 (service_id), INDEX IDX_D79F6B11EDDDAE19 (cleaner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, icon VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE service_cleaner (service_id INT NOT NULL, cleaner_id INT NOT NULL, INDEX IDX_A1001538ED5CA9E6 (service_id), INDEX IDX_A1001538EDDDAE19 (cleaner_id), PRIMARY KEY(service_id, cleaner_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, password VARCHAR(255) NOT NULL, first_name VARCHAR(255) NOT NULL, last_name VARCHAR(255) DEFAULT NULL, image VARCHAR(255) DEFAULT NULL, discr VARCHAR(255) NOT NULL, UNIQUE INDEX UNIQ_8D93D649E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL, available_at DATETIME NOT NULL, delivered_at DATETIME DEFAULT NULL, INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE cleaner ADD CONSTRAINT FK_6E8447A4BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE customer ADD CONSTRAINT FK_81398E09BF396750 FOREIGN KEY (id) REFERENCES user (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE housework ADD CONSTRAINT FK_18474A479395C3F3 FOREIGN KEY (customer_id) REFERENCES customer (id)');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B11F1BA2DD1 FOREIGN KEY (housework_id) REFERENCES housework (id)');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B11ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id)');
        $this->addSql('ALTER TABLE participant ADD CONSTRAINT FK_D79F6B11EDDDAE19 FOREIGN KEY (cleaner_id) REFERENCES cleaner (id)');
        $this->addSql('ALTER TABLE service_cleaner ADD CONSTRAINT FK_A1001538ED5CA9E6 FOREIGN KEY (service_id) REFERENCES service (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE service_cleaner ADD CONSTRAINT FK_A1001538EDDDAE19 FOREIGN KEY (cleaner_id) REFERENCES cleaner (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE cleaner DROP FOREIGN KEY FK_6E8447A4BF396750');
        $this->addSql('ALTER TABLE customer DROP FOREIGN KEY FK_81398E09BF396750');
        $this->addSql('ALTER TABLE housework DROP FOREIGN KEY FK_18474A479395C3F3');
        $this->addSql('ALTER TABLE participant DROP FOREIGN KEY FK_D79F6B11F1BA2DD1');
        $this->addSql('ALTER TABLE participant DROP FOREIGN KEY FK_D79F6B11ED5CA9E6');
        $this->addSql('ALTER TABLE participant DROP FOREIGN KEY FK_D79F6B11EDDDAE19');
        $this->addSql('ALTER TABLE service_cleaner DROP FOREIGN KEY FK_A1001538ED5CA9E6');
        $this->addSql('ALTER TABLE service_cleaner DROP FOREIGN KEY FK_A1001538EDDDAE19');
        $this->addSql('DROP TABLE cleaner');
        $this->addSql('DROP TABLE customer');
        $this->addSql('DROP TABLE housework');
        $this->addSql('DROP TABLE participant');
        $this->addSql('DROP TABLE service');
        $this->addSql('DROP TABLE service_cleaner');
        $this->addSql('DROP TABLE user');
        $this->addSql('DROP TABLE messenger_messages');
    }
}

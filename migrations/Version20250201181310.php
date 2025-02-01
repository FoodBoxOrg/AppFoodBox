<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250201181310 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE food (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(32) NOT NULL, origin VARCHAR(32) DEFAULT NULL, image_id INT DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE food_tags (id INT AUTO_INCREMENT NOT NULL, food_id INT NOT NULL, tag_id INT NOT NULL, INDEX IDX_DA9B3CF7BA8E87C4 (food_id), INDEX IDX_DA9B3CF7BAD26311 (tag_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE images (id INT AUTO_INCREMENT NOT NULL, url VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE review (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, food_id INT NOT NULL, rate INT NOT NULL, comment LONGTEXT DEFAULT NULL, INDEX IDX_794381C6A76ED395 (user_id), INDEX IDX_794381C6BA8E87C4 (food_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE tags (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(16) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, username VARCHAR(30) NOT NULL, imageurl VARCHAR(255) DEFAULT NULL, is_verified TINYINT(1) NOT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE food_tags ADD CONSTRAINT FK_DA9B3CF7BA8E87C4 FOREIGN KEY (food_id) REFERENCES food (id)');
        $this->addSql('ALTER TABLE food_tags ADD CONSTRAINT FK_DA9B3CF7BAD26311 FOREIGN KEY (tag_id) REFERENCES tags (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6A76ED395 FOREIGN KEY (user_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE review ADD CONSTRAINT FK_794381C6BA8E87C4 FOREIGN KEY (food_id) REFERENCES food (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE food_tags DROP FOREIGN KEY FK_DA9B3CF7BA8E87C4');
        $this->addSql('ALTER TABLE food_tags DROP FOREIGN KEY FK_DA9B3CF7BAD26311');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6A76ED395');
        $this->addSql('ALTER TABLE review DROP FOREIGN KEY FK_794381C6BA8E87C4');
        $this->addSql('DROP TABLE food');
        $this->addSql('DROP TABLE food_tags');
        $this->addSql('DROP TABLE images');
        $this->addSql('DROP TABLE review');
        $this->addSql('DROP TABLE tags');
        $this->addSql('DROP TABLE user');
    }
}

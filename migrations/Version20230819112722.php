<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230819112722 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE souscategory DROP FOREIGN KEY FK_510D80F14584665A');
        $this->addSql('ALTER TABLE souscategory DROP FOREIGN KEY FK_510D80F112469DE2');
        $this->addSql('DROP TABLE souscategory');
        $this->addSql('ALTER TABLE category ADD description VARCHAR(255) DEFAULT NULL, DROP souscategory');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE souscategory (id INT AUTO_INCREMENT NOT NULL, product_id INT DEFAULT NULL, category_id INT DEFAULT NULL, name VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, INDEX IDX_510D80F112469DE2 (category_id), INDEX IDX_510D80F14584665A (product_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB COMMENT = \'\' ');
        $this->addSql('ALTER TABLE souscategory ADD CONSTRAINT FK_510D80F14584665A FOREIGN KEY (product_id) REFERENCES product (id)');
        $this->addSql('ALTER TABLE souscategory ADD CONSTRAINT FK_510D80F112469DE2 FOREIGN KEY (category_id) REFERENCES category (id)');
        $this->addSql('ALTER TABLE category ADD souscategory VARCHAR(255) NOT NULL, DROP description');
    }
}

<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231227190943 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE account (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', last_modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', uuid VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(511) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE account_token (id INT AUTO_INCREMENT NOT NULL, account_id INT DEFAULT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', last_modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', uuid VARCHAR(255) NOT NULL, value VARCHAR(511) NOT NULL, INDEX IDX_69B8784A9B6B5FBA (account_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE file (id INT AUTO_INCREMENT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', last_modified_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', uuid VARCHAR(255) NOT NULL, name VARCHAR(511) NOT NULL, last_synced DATETIME DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE file_account (file_id INT NOT NULL, account_id INT NOT NULL, INDEX IDX_2650EF9F93CB796C (file_id), INDEX IDX_2650EF9F9B6B5FBA (account_id), PRIMARY KEY(file_id, account_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE account_token ADD CONSTRAINT FK_69B8784A9B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id)');
        $this->addSql('ALTER TABLE file_account ADD CONSTRAINT FK_2650EF9F93CB796C FOREIGN KEY (file_id) REFERENCES file (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE file_account ADD CONSTRAINT FK_2650EF9F9B6B5FBA FOREIGN KEY (account_id) REFERENCES account (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE account_token DROP FOREIGN KEY FK_69B8784A9B6B5FBA');
        $this->addSql('ALTER TABLE file_account DROP FOREIGN KEY FK_2650EF9F93CB796C');
        $this->addSql('ALTER TABLE file_account DROP FOREIGN KEY FK_2650EF9F9B6B5FBA');
        $this->addSql('DROP TABLE account');
        $this->addSql('DROP TABLE account_token');
        $this->addSql('DROP TABLE file');
        $this->addSql('DROP TABLE file_account');
    }
}

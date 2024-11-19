<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241119140755 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE organization (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, name VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, summary LONGTEXT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', UNIQUE INDEX UNIQ_C1EE637CF47645AE (url), INDEX IDX_C1EE637C7E3C61F9 (owner_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE organization_members (organization_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_88725ABC32C8A3DE (organization_id), INDEX IDX_88725ABCA76ED395 (user_id), PRIMARY KEY(organization_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, organization_id INT DEFAULT NULL, title VARCHAR(255) NOT NULL, summary VARCHAR(255) NOT NULL, long_description LONGTEXT NOT NULL, gallery JSON NOT NULL COMMENT \'(DC2Type:json)\', changelog JSON NOT NULL COMMENT \'(DC2Type:json)\', versions JSON NOT NULL COMMENT \'(DC2Type:json)\', is_downloadable TINYINT(1) NOT NULL, download_url VARCHAR(255) DEFAULT NULL, download_count INT NOT NULL, likes_count INT NOT NULL, favorites_count INT NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_2FB3D0EEA76ED395 (user_id), INDEX IDX_2FB3D0EE32C8A3DE (organization_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_likes (project_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_5A9F47E3166D1F9C (project_id), INDEX IDX_5A9F47E3A76ED395 (user_id), PRIMARY KEY(project_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE project_favorites (project_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_F5552522166D1F9C (project_id), INDEX IDX_F5552522A76ED395 (user_id), PRIMARY KEY(project_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE organization ADD CONSTRAINT FK_C1EE637C7E3C61F9 FOREIGN KEY (owner_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE organization_members ADD CONSTRAINT FK_88725ABC32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE organization_members ADD CONSTRAINT FK_88725ABCA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EEA76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id)');
        $this->addSql('ALTER TABLE project ADD CONSTRAINT FK_2FB3D0EE32C8A3DE FOREIGN KEY (organization_id) REFERENCES organization (id)');
        $this->addSql('ALTER TABLE project_likes ADD CONSTRAINT FK_5A9F47E3166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_likes ADD CONSTRAINT FK_5A9F47E3A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_favorites ADD CONSTRAINT FK_F5552522166D1F9C FOREIGN KEY (project_id) REFERENCES project (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE project_favorites ADD CONSTRAINT FK_F5552522A76ED395 FOREIGN KEY (user_id) REFERENCES `user` (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE user ADD display_name VARCHAR(255) DEFAULT NULL, ADD bio LONGTEXT DEFAULT NULL, ADD avatar_url VARCHAR(255) DEFAULT NULL, ADD created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', ADD updated_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE organization DROP FOREIGN KEY FK_C1EE637C7E3C61F9');
        $this->addSql('ALTER TABLE organization_members DROP FOREIGN KEY FK_88725ABC32C8A3DE');
        $this->addSql('ALTER TABLE organization_members DROP FOREIGN KEY FK_88725ABCA76ED395');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EEA76ED395');
        $this->addSql('ALTER TABLE project DROP FOREIGN KEY FK_2FB3D0EE32C8A3DE');
        $this->addSql('ALTER TABLE project_likes DROP FOREIGN KEY FK_5A9F47E3166D1F9C');
        $this->addSql('ALTER TABLE project_likes DROP FOREIGN KEY FK_5A9F47E3A76ED395');
        $this->addSql('ALTER TABLE project_favorites DROP FOREIGN KEY FK_F5552522166D1F9C');
        $this->addSql('ALTER TABLE project_favorites DROP FOREIGN KEY FK_F5552522A76ED395');
        $this->addSql('DROP TABLE organization');
        $this->addSql('DROP TABLE organization_members');
        $this->addSql('DROP TABLE project');
        $this->addSql('DROP TABLE project_likes');
        $this->addSql('DROP TABLE project_favorites');
        $this->addSql('ALTER TABLE `user` DROP display_name, DROP bio, DROP avatar_url, DROP created_at, DROP updated_at');
    }
}

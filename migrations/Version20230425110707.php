<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230425110707 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE gender (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(20) NOT NULL, title VARCHAR(50) NOT NULL, enabled TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE user_profile (id INT AUTO_INCREMENT NOT NULL, owner_id INT NOT NULL, gender_id INT NOT NULL, interest_id INT NOT NULL, name VARCHAR(50) NOT NULL, birth_date DATE NOT NULL, description LONGTEXT DEFAULT NULL, UNIQUE INDEX UNIQ_D95AB4057E3C61F9 (owner_id), INDEX IDX_D95AB405708A0E0 (gender_id), INDEX IDX_D95AB4055A95FF89 (interest_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE user_profile ADD CONSTRAINT FK_D95AB4057E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('ALTER TABLE user_profile ADD CONSTRAINT FK_D95AB405708A0E0 FOREIGN KEY (gender_id) REFERENCES gender (id)');
        $this->addSql('ALTER TABLE user_profile ADD CONSTRAINT FK_D95AB4055A95FF89 FOREIGN KEY (interest_id) REFERENCES gender (id)');
        $this->addSql('ALTER TABLE user CHANGE username username VARCHAR(20) NOT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_profile DROP FOREIGN KEY FK_D95AB4057E3C61F9');
        $this->addSql('ALTER TABLE user_profile DROP FOREIGN KEY FK_D95AB405708A0E0');
        $this->addSql('ALTER TABLE user_profile DROP FOREIGN KEY FK_D95AB4055A95FF89');
        $this->addSql('DROP TABLE gender');
        $this->addSql('DROP TABLE user_profile');
        $this->addSql('ALTER TABLE user CHANGE username username VARCHAR(50) NOT NULL');
    }
}

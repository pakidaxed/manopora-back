<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230427152740 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_picture ADD owner_id INT NOT NULL');
        $this->addSql('ALTER TABLE user_picture ADD CONSTRAINT FK_4ED651837E3C61F9 FOREIGN KEY (owner_id) REFERENCES user (id)');
        $this->addSql('CREATE INDEX IDX_4ED651837E3C61F9 ON user_picture (owner_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE user_picture DROP FOREIGN KEY FK_4ED651837E3C61F9');
        $this->addSql('DROP INDEX IDX_4ED651837E3C61F9 ON user_picture');
        $this->addSql('ALTER TABLE user_picture DROP owner_id');
    }
}

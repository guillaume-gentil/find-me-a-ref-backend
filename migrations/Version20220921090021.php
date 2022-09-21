<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220921090021 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE game (id INT AUTO_INCREMENT NOT NULL, arena_id INT NOT NULL, type_id INT NOT NULL, date DATETIME NOT NULL, created_at DATETIME NOT NULL COMMENT \'(DC2Type:datetime_immutable)\', updated_at DATETIME DEFAULT NULL COMMENT \'(DC2Type:datetime_immutable)\', INDEX IDX_232B318C663565CF (arena_id), INDEX IDX_232B318CC54C8C93 (type_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_team (game_id INT NOT NULL, team_id INT NOT NULL, INDEX IDX_2FF5CA33E48FD905 (game_id), INDEX IDX_2FF5CA33296CD8AE (team_id), PRIMARY KEY(game_id, team_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE game_user (game_id INT NOT NULL, user_id INT NOT NULL, INDEX IDX_6686BA65E48FD905 (game_id), INDEX IDX_6686BA65A76ED395 (user_id), PRIMARY KEY(game_id, user_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318C663565CF FOREIGN KEY (arena_id) REFERENCES arena (id)');
        $this->addSql('ALTER TABLE game ADD CONSTRAINT FK_232B318CC54C8C93 FOREIGN KEY (type_id) REFERENCES type (id)');
        $this->addSql('ALTER TABLE game_team ADD CONSTRAINT FK_2FF5CA33E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_team ADD CONSTRAINT FK_2FF5CA33296CD8AE FOREIGN KEY (team_id) REFERENCES team (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_user ADD CONSTRAINT FK_6686BA65E48FD905 FOREIGN KEY (game_id) REFERENCES game (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE game_user ADD CONSTRAINT FK_6686BA65A76ED395 FOREIGN KEY (user_id) REFERENCES user (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318C663565CF');
        $this->addSql('ALTER TABLE game DROP FOREIGN KEY FK_232B318CC54C8C93');
        $this->addSql('ALTER TABLE game_team DROP FOREIGN KEY FK_2FF5CA33E48FD905');
        $this->addSql('ALTER TABLE game_team DROP FOREIGN KEY FK_2FF5CA33296CD8AE');
        $this->addSql('ALTER TABLE game_user DROP FOREIGN KEY FK_6686BA65E48FD905');
        $this->addSql('ALTER TABLE game_user DROP FOREIGN KEY FK_6686BA65A76ED395');
        $this->addSql('DROP TABLE game');
        $this->addSql('DROP TABLE game_team');
        $this->addSql('DROP TABLE game_user');
    }
}

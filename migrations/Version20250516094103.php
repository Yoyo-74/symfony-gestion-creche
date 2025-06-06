<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250516094103 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            CREATE TABLE calendar (id INT AUTO_INCREMENT NOT NULL, date DATE NOT NULL, day VARCHAR(20) NOT NULL, week INT NOT NULL, isopen TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE calendar_childs (id INT AUTO_INCREMENT NOT NULL, child_id INT NOT NULL, idcalendar_id INT NOT NULL, date DATE NOT NULL, heure_arrivee TIME NOT NULL, heure_depart TIME NOT NULL, ispresent TINYINT(1) NOT NULL, INDEX IDX_F3F8FC6FDD62C21B (child_id), INDEX IDX_F3F8FC6FCC81D267 (idcalendar_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE childs (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, date_naissance DATE NOT NULL, genre VARCHAR(20) NOT NULL, allergies VARCHAR(255) DEFAULT NULL, remarques_medicales VARCHAR(255) DEFAULT NULL, revenus INT DEFAULT NULL, date_entree DATE DEFAULT NULL, date_sortie DATE DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE responsables (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, prenom VARCHAR(255) NOT NULL, email VARCHAR(255) DEFAULT NULL, tel VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE responsables_childs (id INT AUTO_INCREMENT NOT NULL, responsable_id INT NOT NULL, child_id INT NOT NULL, lien VARCHAR(255) NOT NULL, INDEX IDX_12BBC85A53C59D72 (responsable_id), INDEX IDX_12BBC85ADD62C21B (child_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE users (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(180) NOT NULL, roles JSON NOT NULL, password VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, premnom VARCHAR(255) NOT NULL, tel VARCHAR(255) NOT NULL, role VARCHAR(255) NOT NULL, adress VARCHAR(255) DEFAULT NULL, cp VARCHAR(10) DEFAULT NULL, ville VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_IDENTIFIER_EMAIL (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE users_childs (id INT AUTO_INCREMENT NOT NULL, child_id INT DEFAULT NULL, user_id INT DEFAULT NULL, INDEX IDX_56796AA3DD62C21B (child_id), INDEX IDX_56796AA3A76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            CREATE TABLE messenger_messages (id BIGINT AUTO_INCREMENT NOT NULL, body LONGTEXT NOT NULL, headers LONGTEXT NOT NULL, queue_name VARCHAR(190) NOT NULL, created_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', available_at DATETIME NOT NULL COMMENT '(DC2Type:datetime_immutable)', delivered_at DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)', INDEX IDX_75EA56E0FB7336F0 (queue_name), INDEX IDX_75EA56E0E3BD61CE (available_at), INDEX IDX_75EA56E016BA31DB (delivered_at), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE calendar_childs ADD CONSTRAINT FK_F3F8FC6FDD62C21B FOREIGN KEY (child_id) REFERENCES childs (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE calendar_childs ADD CONSTRAINT FK_F3F8FC6FCC81D267 FOREIGN KEY (idcalendar_id) REFERENCES calendar (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE responsables_childs ADD CONSTRAINT FK_12BBC85A53C59D72 FOREIGN KEY (responsable_id) REFERENCES responsables (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE responsables_childs ADD CONSTRAINT FK_12BBC85ADD62C21B FOREIGN KEY (child_id) REFERENCES childs (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users_childs ADD CONSTRAINT FK_56796AA3DD62C21B FOREIGN KEY (child_id) REFERENCES childs (id)
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users_childs ADD CONSTRAINT FK_56796AA3A76ED395 FOREIGN KEY (user_id) REFERENCES users (id)
        SQL);
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql(<<<'SQL'
            ALTER TABLE calendar_childs DROP FOREIGN KEY FK_F3F8FC6FDD62C21B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE calendar_childs DROP FOREIGN KEY FK_F3F8FC6FCC81D267
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE responsables_childs DROP FOREIGN KEY FK_12BBC85A53C59D72
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE responsables_childs DROP FOREIGN KEY FK_12BBC85ADD62C21B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users_childs DROP FOREIGN KEY FK_56796AA3DD62C21B
        SQL);
        $this->addSql(<<<'SQL'
            ALTER TABLE users_childs DROP FOREIGN KEY FK_56796AA3A76ED395
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE calendar
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE calendar_childs
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE childs
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE responsables
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE responsables_childs
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE users
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE users_childs
        SQL);
        $this->addSql(<<<'SQL'
            DROP TABLE messenger_messages
        SQL);
    }
}

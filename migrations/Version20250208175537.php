<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250208175537 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE `admin` (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, api_token VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_880E0D76E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE client (id INT AUTO_INCREMENT NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, api_token VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_C7440455E7927C74 (email), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE commande (id INT AUTO_INCREMENT NOT NULL, idclient_id INT DEFAULT NULL, date_commande DATE NOT NULL, montant_total NUMERIC(10, 2) NOT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_6EEAA67D67F0C0D4 (idclient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE detail_commande (id INT AUTO_INCREMENT NOT NULL, id_commande_id INT DEFAULT NULL, id_plat_id INT DEFAULT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_98344FA69AF8E3A3 (id_commande_id), INDEX IDX_98344FA69A01C10 (id_plat_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ingredient (id INT AUTO_INCREMENT NOT NULL, nom_ingredient VARCHAR(255) NOT NULL, image VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE ingredient_plat (id INT AUTO_INCREMENT NOT NULL, plat_id INT DEFAULT NULL, ingredient_id INT DEFAULT NULL, INDEX IDX_7E691291D73DB560 (plat_id), INDEX IDX_7E691291933FE08C (ingredient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE plat (id INT AUTO_INCREMENT NOT NULL, nom_plat VARCHAR(255) NOT NULL, prix_unitaire NUMERIC(10, 2) NOT NULL, temps_cuisson TIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE stock (id INT AUTO_INCREMENT NOT NULL, id_ingredient_id INT DEFAULT NULL, quantite INT NOT NULL, date_mouvement DATE NOT NULL, status VARCHAR(255) NOT NULL, INDEX IDX_4B3656602D1731E9 (id_ingredient_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE commande ADD CONSTRAINT FK_6EEAA67D67F0C0D4 FOREIGN KEY (idclient_id) REFERENCES client (id)');
        $this->addSql('ALTER TABLE detail_commande ADD CONSTRAINT FK_98344FA69AF8E3A3 FOREIGN KEY (id_commande_id) REFERENCES commande (id)');
        $this->addSql('ALTER TABLE detail_commande ADD CONSTRAINT FK_98344FA69A01C10 FOREIGN KEY (id_plat_id) REFERENCES plat (id)');
        $this->addSql('ALTER TABLE ingredient_plat ADD CONSTRAINT FK_7E691291D73DB560 FOREIGN KEY (plat_id) REFERENCES plat (id)');
        $this->addSql('ALTER TABLE ingredient_plat ADD CONSTRAINT FK_7E691291933FE08C FOREIGN KEY (ingredient_id) REFERENCES ingredient (id)');
        $this->addSql('ALTER TABLE stock ADD CONSTRAINT FK_4B3656602D1731E9 FOREIGN KEY (id_ingredient_id) REFERENCES ingredient (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE commande DROP FOREIGN KEY FK_6EEAA67D67F0C0D4');
        $this->addSql('ALTER TABLE detail_commande DROP FOREIGN KEY FK_98344FA69AF8E3A3');
        $this->addSql('ALTER TABLE detail_commande DROP FOREIGN KEY FK_98344FA69A01C10');
        $this->addSql('ALTER TABLE ingredient_plat DROP FOREIGN KEY FK_7E691291D73DB560');
        $this->addSql('ALTER TABLE ingredient_plat DROP FOREIGN KEY FK_7E691291933FE08C');
        $this->addSql('ALTER TABLE stock DROP FOREIGN KEY FK_4B3656602D1731E9');
        $this->addSql('DROP TABLE `admin`');
        $this->addSql('DROP TABLE client');
        $this->addSql('DROP TABLE commande');
        $this->addSql('DROP TABLE detail_commande');
        $this->addSql('DROP TABLE ingredient');
        $this->addSql('DROP TABLE ingredient_plat');
        $this->addSql('DROP TABLE plat');
        $this->addSql('DROP TABLE stock');
    }
}

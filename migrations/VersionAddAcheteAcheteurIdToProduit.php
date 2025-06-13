<?php
// Ce fichier de migration ajoute les champs 'achete' et 'acheteur_id' à la table produit

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class VersionAddAcheteAcheteurIdToProduit extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Ajoute les champs achete (bool) et acheteur_id (int, nullable) à la table produit';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE produit ADD achete TINYINT(1) DEFAULT 0 NOT NULL, ADD acheteur_id INT DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE produit DROP achete, DROP acheteur_id');
    }
}

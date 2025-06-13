<?php
// Migration pour supprimer les colonnes achete, acheteur_id et utilisateur_id de la table produit

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class VersionRemoveAchatFieldsFromProduit extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Supprime les colonnes achete, acheteur_id et utilisateur_id de la table produit';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('ALTER TABLE produit DROP COLUMN achete, DROP COLUMN acheteur_id, DROP FOREIGN KEY FK_29A5EC27FB88E14F, DROP COLUMN utilisateur_id');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('ALTER TABLE produit ADD achete TINYINT(1) DEFAULT 0 NOT NULL, ADD acheteur_id INT DEFAULT NULL, ADD utilisateur_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE produit ADD CONSTRAINT FK_29A5EC27FB88E14F FOREIGN KEY (utilisateur_id) REFERENCES user (id)');
    }
}

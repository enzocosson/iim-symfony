<?php
namespace App\DataFixtures;

use App\Entity\Produit;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProduitFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $produits = [
            [
                'nom' => 'Porsche 911 Carrera',
                'prix' => 120000,
                'category' => 'Sportive',
                'description' => 'La Porsche 911 Carrera est une icône des voitures sportives allemandes.',
            ],
            [
                'nom' => 'Tesla Model S',
                'prix' => 95000,
                'category' => 'Électrique',
                'description' => 'La Tesla Model S est une berline électrique haut de gamme, rapide et innovante.',
            ],
            [
                'nom' => 'Renault Clio',
                'prix' => 18000,
                'category' => 'Citadine',
                'description' => 'La Renault Clio est une citadine française très populaire.',
            ],
            [
                'nom' => 'BMW X5',
                'prix' => 70000,
                'category' => 'SUV',
                'description' => 'Le BMW X5 est un SUV premium alliant confort et puissance.',
            ],
            [
                'nom' => 'Peugeot 208',
                'prix' => 17000,
                'category' => 'Citadine',
                'description' => 'La Peugeot 208 est une citadine moderne et économique.',
            ],
            [
                'nom' => 'Ferrari F8 Tributo',
                'prix' => 250000,
                'category' => 'Sportive',
                'description' => 'La Ferrari F8 Tributo est une supercar italienne de rêve.',
            ],
        ];

        foreach ($produits as $data) {
            $produit = new Produit();
            $produit->setNom($data['nom']);
            $produit->setPrix($data['prix']);
            $produit->setCategory($data['category']);
            $produit->setDescription($data['description']);
            $produit->setCreatedAt(new \DateTimeImmutable());
            $produit->setUpdatedAt(new \DateTimeImmutable());
            $manager->persist($produit);
        }
        $manager->flush();
    }
}

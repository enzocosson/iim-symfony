<?php

// src/Repository/ProduitRepository.php

namespace App\Repository;

use App\Entity\Produit;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Produit>
 */
class ProduitRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Produit::class);
    }

    /**
     * Retourne les produits créés par un user donné
     * 
     * @param User $user
     * @return Produit[]
     */
    public function findByUserCreated(User $user): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.createdBy = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
}

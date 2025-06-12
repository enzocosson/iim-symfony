<?php
// src/Repository/NotificationRepository.php

namespace App\Repository;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Notification>
 */
class NotificationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Notification::class);
    }

    /**
     * Retourne les notifications visibles pour un utilisateur donné
     */
    public function findVisibleForUser(User $user): array
    {
        $qb = $this->createQueryBuilder('n');
        if ($user->isAdmin()) {
            // L'admin ne voit pas les notifications de type 'point user'
            $qb->where('(n.utilisateur = :user OR n.utilisateur IS NULL)')
               ->andWhere('n.type != :typeUser')
               ->setParameter('typeUser', 'point user');
        } else {
            // Les users ne voient pas les notifications de type 'point'
            $qb->where('n.utilisateur = :user')
               ->andWhere('n.type != :typeAdmin')
               ->setParameter('typeAdmin', 'point');
        }
        $qb->setParameter('user', $user);
        $qb->orderBy('n.createdAt', 'DESC');
        return $qb->getQuery()->getResult();
    }
}

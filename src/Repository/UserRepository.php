<?php

// src/Repository/UserRepository.php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<User>
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, User::class);
    }

    /**
     * Retourne tous les utilisateurs ayant un rôle donné
     * @param string $role
     * @return User[]
     */
    public function findByRole(string $role): array
    {
        $users = $this->findAll();
        return array_filter($users, function($user) use ($role) {
            return in_array($role, $user->getRoles());
        });
    }
}

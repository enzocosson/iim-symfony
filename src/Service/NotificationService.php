<?php
namespace App\Service;

use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class NotificationService
{
    private EntityManagerInterface $em;
    private User $adminUser;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function setAdminUser(User $admin): void
    {
        $this->adminUser = $admin;
    }

    /**
     * Crée et persiste une notification pour l'admin
     * @param string $typeAction Ex: 'Création', 'Modification', 'Suppression', 'Achat', 'Désactivation'
     * @param string $entityName Ex: 'Produit', 'User'
     * @param string $details Nom ou info complémentaire
     */
    public function notifyAdmin(string $typeAction, string $entityName, string $details): void
    {
        if (!isset($this->adminUser)) {
            // Récupérer l'admin par défaut (exemple : le premier admin)
            $admin = $this->em->getRepository(User::class)->findOneBy(['roles' => ['ROLE_ADMIN']]);
            if (!$admin) {
                return; // Pas d'admin trouvé
            }
            $this->adminUser = $admin;
        } else {
            $admin = $this->adminUser;
        }

        $label = sprintf(
            '%s %s "%s" le %s',
            $typeAction,
            $entityName,
            $details,
            (new \DateTimeImmutable())->format('d/m/Y H:i')
        );

        $notif = new Notification();
        $notif->setLabel($label);
        $notif->setUser($admin);

        $this->em->persist($notif);
        $this->em->flush();
    }

    public function notifyUserDesactive(User $user): void
    {
        $notification = new Notification();
        $notification->setLabel('Votre compte a été désactivé. Vous ne pouvez plus acheter de produits.');
        $notification->setUtilisateur($user);
        $notification->setCreatedAt(new \DateTimeImmutable());
        $notification->setUpdatedAt(new \DateTimeImmutable());
        $this->em->persist($notification);
        $this->em->flush();
    }
}

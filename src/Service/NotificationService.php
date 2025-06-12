<?php
namespace App\Service;

use App\Entity\Notification;
use App\Entity\User;
use App\Message\NotificationMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\MessageBusInterface;

class NotificationService
{
    private EntityManagerInterface $em;
    private User $adminUser;
    private MessageBusInterface $bus;

    public function __construct(EntityManagerInterface $em, MessageBusInterface $bus)
    {
        $this->em = $em;
        $this->bus = $bus;
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
        $label = sprintf(
            '%s %s "%s" le %s',
            $typeAction,
            $entityName,
            $details,
            (new \DateTimeImmutable())->format('d/m/Y H:i')
        );
        $this->bus->dispatch(new NotificationMessage(strtolower($typeAction), $label));
    }

    public function notifyUserDesactive(User $user): void
    {
        $label = 'Votre compte a été désactivé. Vous ne pouvez plus acheter de produits.';
        $this->bus->dispatch(new NotificationMessage('desactivation', $label, $user->getId()));
    }

    // --- NOTIFICATION ADMIN POINTS ---
    // Cette méthode a été déplacée ici, supprimez toute autre déclaration de notifyAdminPoints dans ce fichier.
    public function notifyAdminPoints(User $admin, User $user, int $points): void
    {
        $label = sprintf('%s a obtenu %d points.', $user->getNom() . ' ' . $user->getPrenom(), $points);
        $this->bus->dispatch(new NotificationMessage('points', $label, $admin->getId()));
    }

    public function notifyUserPoints(User $user, int $points, string $type = 'points'): void
    {
        $label = sprintf('Vous avez reçu %d points.', $points);
        $this->bus->dispatch(new NotificationMessage($type, $label, $user->getId()));
    }


}

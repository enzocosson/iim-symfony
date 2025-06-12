<?php
// src/MessageHandler/AddPointsToActiveUsersMessageHandler.php
namespace App\MessageHandler;

use App\Message\AddPointsToActiveUsersMessage;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use App\Service\NotificationService;

#[AsMessageHandler]
class AddPointsToActiveUsersMessageHandler
{
    private UserRepository $userRepository;
    private EntityManagerInterface $em;
    private NotificationService $notificationService;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $em, NotificationService $notificationService)
    {
        $this->userRepository = $userRepository;
        $this->em = $em;
        $this->notificationService = $notificationService;
    }

    public function __invoke(AddPointsToActiveUsersMessage $message)
    {
        $users = $this->userRepository->findBy(['actif' => 0]);
        $userNames = [];
        foreach ($users as $user) {
            $user->setPoints($user->getPoints() + $message->getPoints());
            $this->notificationService->notifyUserPoints($user, $message->getPoints(), 'point user');
            $userNames[] = $user->getNom() . ' ' . $user->getPrenom();
        }
        $this->em->flush();
        // Notification admin avec la liste des users ayant reçu les points
        if (count($userNames) > 0) {
            $this->notificationService->notifyAdmin(
                'point',
                'Utilisateurs',
                'Points ajoutés à : ' . implode(', ', $userNames)
            );
        }
    }
}

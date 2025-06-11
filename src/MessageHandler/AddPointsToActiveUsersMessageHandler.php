<?php
// src/MessageHandler/AddPointsToActiveUsersMessageHandler.php
namespace App\MessageHandler;

use App\Message\AddPointsToActiveUsersMessage;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class AddPointsToActiveUsersMessageHandler
{
    private UserRepository $userRepository;
    private EntityManagerInterface $em;

    public function __construct(UserRepository $userRepository, EntityManagerInterface $em)
    {
        $this->userRepository = $userRepository;
        $this->em = $em;
    }

    public function __invoke(AddPointsToActiveUsersMessage $message)
    {
        $users = $this->userRepository->findBy(['actif' => true]);
        foreach ($users as $user) {
            $user->setPoints($user->getPoints() + $message->getPoints());
        }
        $this->em->flush();
    }
}

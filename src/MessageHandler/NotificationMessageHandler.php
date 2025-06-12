<?php
namespace App\MessageHandler;

use App\Message\NotificationMessage;
use App\Entity\Notification;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class NotificationMessageHandler
{
    public function __construct(private EntityManagerInterface $em) {}

    public function __invoke(NotificationMessage $message)
    {
        $notif = new Notification();
        $notif->setLabel($message->getLabel());
        $notif->setType($message->getType());
        $notif->setCreatedAt(new \DateTimeImmutable());
        $notif->setUpdatedAt(new \DateTimeImmutable());
        if ($message->getUserId()) {
            $user = $this->em->getRepository(User::class)->find($message->getUserId());
            if ($user) {
                $notif->setUtilisateur($user);
            }
        }
        $this->em->persist($notif);
        $this->em->flush();
    }
}

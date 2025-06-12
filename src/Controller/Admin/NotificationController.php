<?php
// src/Controller/Admin/NotificationController.php
namespace App\Controller\Admin;

use App\Entity\Notification;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/notifications')]
class NotificationController extends AbstractController
{
    #[Route('/', name: 'admin_notification_index', methods: ['GET'])]
    public function index(NotificationRepository $notificationRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $user = $this->getUser();
        $notifications = $notificationRepository->findVisibleForUser($user);

        return $this->render('admin/notification/index.html.twig', [
            'notifications' => $notifications,
        ]);
    }

    #[Route('/{id}/read', name: 'admin_notification_read', methods: ['POST'])]
    public function markAsRead(Request $request, Notification $notification, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        if ($this->isCsrfTokenValid('read'.$notification->getId(), $request->request->get('_token'))) {
            $notification->setIsRead(true);
            $em->flush();
            $this->addFlash('success', 'Notification marquée comme lue.');
        }

        return $this->redirectToRoute('admin_notification_index');
    }
}

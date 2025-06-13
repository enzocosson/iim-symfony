<?php
// src/Controller/ProfileController.php
namespace App\Controller;

use App\Form\ProfileType;
use App\Repository\NotificationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends AbstractController
{
    #[Route('/profile/edit', name: 'profile_edit')]
    public function edit(Request $request, EntityManagerInterface $em, \App\Service\NotificationService $notificationService): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $form = $this->createForm(ProfileType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $notificationService->notifyAdmin('modification profil', 'Profil', $user->getNom() . ' ' . $user->getPrenom());
            $this->addFlash('success', 'Profil mis à jour !');
            return $this->redirectToRoute('profile');
        }
        return $this->render('profile/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/notifications', name: 'user_notification_index')]
    public function notifications(NotificationRepository $notificationRepository): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        $notifications = $notificationRepository->findVisibleForUser($user);
        return $this->render('profile/notifications.html.twig', [
            'notifications' => $notifications,
        ]);
    }

    #[Route('/profile', name: 'profile')]
    public function index(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }
        return $this->render('profile/index.html.twig', [
            'user' => $user,
        ]);
    }
}

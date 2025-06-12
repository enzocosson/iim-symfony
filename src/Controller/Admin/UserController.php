<?php
// src/Controller/Admin/UserController.php
namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/users')]
class UserController extends AbstractController
{
    private function denyAdminAccessUnlessGranted()
    {
        if (!$this->isGranted('ROLE_ADMIN')) {
            return $this->redirectToRoute('access_denied_admin');
        }
        return null;
    }

    #[Route('/access-denied', name: 'access_denied_admin')]
    public function accessDenied(): Response
    {
        return $this->render('admin/access_denied.html.twig');
    }

    #[Route('/', name: 'admin_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        if ($redirect = $this->denyAdminAccessUnlessGranted()) return $redirect;
        $users = $userRepository->findAll();

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/new', name: 'admin_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        if ($redirect = $this->denyAdminAccessUnlessGranted()) return $redirect;
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($user);
            $em->flush();
            $this->addFlash('success', 'Utilisateur créé.');
            return $this->redirectToRoute('admin_user_index');
        }
        return $this->render('admin/user/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/list', name: 'admin_user_list', methods: ['GET'])]
    public function list(UserRepository $userRepository): Response
    {
        if ($redirect = $this->denyAdminAccessUnlessGranted()) return $redirect;
        $users = $userRepository->findAll();
        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/{id}', name: 'admin_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        if ($redirect = $this->denyAdminAccessUnlessGranted()) return $redirect;
        return $this->render('admin/user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $em): Response
    {
        if ($redirect = $this->denyAdminAccessUnlessGranted()) return $redirect;
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Utilisateur modifié.');
            return $this->redirectToRoute('admin_user_index');
        }
        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/add-points', name: 'admin_user_add_points', methods: ['POST'])]
    public function addPointsToAllActiveUsers(\Symfony\Component\Messenger\MessageBusInterface $bus, Request $request): Response
    {
        if ($redirect = $this->denyAdminAccessUnlessGranted()) return $redirect;
        if ($this->isCsrfTokenValid('add_points', $request->request->get('_token'))) {
            $bus->dispatch(new \App\Message\AddPointsToActiveUsersMessage(1000));
            $this->addFlash('success', 'Ajout de 1000 points à tous les utilisateurs actifs lancé (traité en tâche de fond).');
        }
        return $this->redirectToRoute('admin_user_index');
    }

    #[Route('/{id}', name: 'admin_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $em): Response
    {
        if ($redirect = $this->denyAdminAccessUnlessGranted()) return $redirect;
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $em->remove($user);
            $em->flush();
            $this->addFlash('success', 'Utilisateur supprimé.');
        }
        return $this->redirectToRoute('admin_user_index');
    }

    #[Route('/{id}/activer', name: 'admin_user_activer', methods: ['POST'])]
    public function activer(User $user, EntityManagerInterface $em): Response
    {
        if ($redirect = $this->denyAdminAccessUnlessGranted()) return $redirect;
        $user->setActif(0);
        $em->flush();
        $this->addFlash('success', 'Utilisateur activé.');
        return $this->redirectToRoute('admin_user_index');
    }

    #[Route('/{id}/desactiver', name: 'admin_user_desactiver', methods: ['POST'])]
    public function desactiver(User $user, EntityManagerInterface $em, \App\Service\NotificationService $notificationService): Response
    {
        if ($redirect = $this->denyAdminAccessUnlessGranted()) return $redirect;
        $user->setActif(1);
        $em->flush();
        $notificationService->notifyUserDesactive($user);
        $this->addFlash('success', 'Utilisateur désactivé et notifié.');
        return $this->redirectToRoute('admin_user_index');
    }
}

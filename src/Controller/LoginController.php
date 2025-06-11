<?php

namespace App\Controller;

use App\Form\LoginFormType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Si l'utilisateur est déjà connecté, on le redirige vers /home
        if ($this->getUser()) {
            return $this->redirectToRoute('app_home');
        }

        // Récupère la dernière erreur d'authentification et le dernier email saisi
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername() ?? '';
        $form = $this->createForm(LoginFormType::class, null, [
            'last_username' => $lastUsername,
        ]);

        return $this->render('login/login.html.twig', [
            'form' => $form->createView(),
            'error' => $error,
        ]);
    }
}

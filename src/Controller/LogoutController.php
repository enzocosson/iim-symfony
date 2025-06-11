<?php

// src/Controller/LogoutController.php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LogoutController extends AbstractController
{
    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Ce code ne sera jamais exécuté
        throw new \Exception('Ne jamais atteindre ce point - Symfony intercepte cette route.');
    }
}

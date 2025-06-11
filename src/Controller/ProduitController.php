<?php
// src/Controller/ProduitController.php
namespace App\Controller;

use App\Entity\Produit;
use App\Repository\ProduitRepository;
use App\Service\NotificationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProduitController extends AbstractController
{
    #[Route('/produits', name: 'produit_index')]
    public function index(ProduitRepository $produitRepository): Response
    {
        $produits = $produitRepository->findAll();
        return $this->render('produit/index.html.twig', [
            'produits' => $produits,
        ]);
    }

    #[Route('/produits/{id}', name: 'produit_show')]
    public function show(Produit $produit): Response
    {
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/produits/{id}/acheter', name: 'produit_acheter', methods: ['POST'])]
    public function acheter(Produit $produit, EntityManagerInterface $em, NotificationService $notificationService): Response
    {
        $user = $this->getUser();
        if (!$user || !$user->isActif()) {
            $this->addFlash('danger', "Vous ne pouvez pas acheter de produit (compte désactivé).");
            // Notifier le user s'il est désactivé
            if ($user) {
                $notificationService->notifyUserDesactive($user);
            }
            return $this->redirectToRoute('produit_show', ['id' => $produit->getId()]);
        }
        if ($user->getPoints() < $produit->getPrix()) {
            $this->addFlash('danger', "Vous n'avez pas assez de points.");
            return $this->redirectToRoute('produit_show', ['id' => $produit->getId()]);
        }
        $user->setPoints($user->getPoints() - $produit->getPrix());
        $em->persist($user);
        $em->flush();
        $this->addFlash('success', 'Achat effectué !');
        // Notifier l'admin
        $notificationService->notifyAdmin('Achat', 'Produit', $produit->getNom() . ' par ' . $user->getEmail());
        return $this->redirectToRoute('produit_index');
    }
}

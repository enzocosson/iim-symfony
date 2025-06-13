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

    #[Route('/produits/{id}', name: 'produit_show', requirements: ['id' => '\\d+'])]
    public function show(Produit $produit): Response
    {
        return $this->render('produit/show.html.twig', [
            'produit' => $produit,
        ]);
    }

    #[Route('/produits/{id}/acheter', name: 'produit_acheter', methods: ['POST'])]
    public function acheter(Produit $produit, Request $request, EntityManagerInterface $em, NotificationService $notificationService): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('danger', 'Vous devez être connecté pour acheter un produit.');
            return $this->redirectToRoute('app_login');
        }
        if ($this->isCsrfTokenValid('acheter' . $produit->getId(), $request->request->get('_token'))) {
            $prix = (int) $produit->getPrix();
            if ($user->getPoints() >= $prix) {
                $user->setPoints($user->getPoints() - $prix);
                $em->persist($user);
                $em->flush();
                $notificationService->notifyAdmin('Achat', 'Produit', $produit->getNom() . ' par ' . $user->getNom());
                $this->addFlash('success', 'Achat effectué ! Il vous reste ' . $user->getPoints() . ' points.');
            } else {
                $this->addFlash('danger', 'Vous n\'avez pas assez de points pour acheter ce produit.');
            }
        } else {
            $this->addFlash('danger', 'Token CSRF invalide.');
        }
        return $this->redirectToRoute('produit_show', ['id' => $produit->getId()]);
    }

    #[Route('/produits/new', name: 'produit_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $produit = new Produit();
        $form = $this->createForm(\App\Form\ProduitType::class, $produit);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Ne pas associer d'utilisateur lors de la création
            $em->persist($produit);
            $em->flush();
            $this->addFlash('success', 'Produit créé avec succès.');
            return $this->redirectToRoute('produit_index');
        }
        return $this->render('produit/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/produits/{id}/edit', name: 'produit_edit')]
    public function edit(Request $request, Produit $produit, EntityManagerInterface $em, NotificationService $notificationService): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        $form = $this->createForm(\App\Form\ProduitType::class, $produit);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $notificationService->notifyAdmin('modification', 'Produit', $produit->getNom());
            $this->addFlash('success', 'Produit modifié avec succès.');
            return $this->redirectToRoute('produit_index');
        }
        return $this->render('produit/edit.html.twig', [
            'form' => $form->createView(),
            'produit' => $produit,
        ]);
    }

    #[Route('/produits/{id}/delete', name: 'produit_delete', methods: ['POST'])]
    public function delete(Request $request, Produit $produit, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');
        if ($this->isCsrfTokenValid('delete' . $produit->getId(), $request->request->get('_token'))) {
            $em->remove($produit);
            $em->flush();
            $this->addFlash('success', 'Produit supprimé.');
        }
        return $this->redirectToRoute('produit_index');
    }
}

<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/sortie', name: 'sortie_')]
class SortieController extends AbstractController
{
    #[Route('/create', name: 'create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $sortie->setOrganisateur($this->getUser());
            $sortie->addParticipant($sortie->getOrganisateur());
            //sauvegarde en base de données
            $entityManager->persist($sortie);
            $entityManager->flush();
            //message de succès et redirection vers liste de sorties
            $this->addFlash('success', 'Sortie ajoutée! Good job!');
            return $this->redirectToRoute('sortie_list');
        }

        return $this->render('sortie/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/list', name: 'list')]
    public function list(SortieRepository $repository): Response
    {
        $sorties = $repository->findAll();
        return $this->render('sortie/list.html.twig', [
        'sorties' => $sorties
        ]);
    }

    #[Route('/details/{id}', name: 'details')]
    public function details(SortieRepository $repository, int $id): Response
    {
        $sortie = $repository->find($id);
        $today = new \DateTime();
        return $this->render('sortie/details.html.twig', [
            'sortie' => $sortie,
            'today' => $today
        ]);
    }
}

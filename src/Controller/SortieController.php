<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieType;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SortieController extends AbstractController
{
    #[Route('/sortie/create', name: 'sortie_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $sortie->setOrganisateur($this->getUser());
            $sortie->addParticipant($sortie->getOrganisateur());
            $entityManager->persist($sortie);
            $entityManager->flush();
            return $this->redirectToRoute('sortie_list');
        }

        return $this->render('sortie/create.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('/sortie/list', name: 'sortie_list')]
    public function list(SortieRepository $repository): Response
    {
        $sorties = $repository->findAll();


        return $this->render('sortie/list.html.twig', [
        'sorties' => $sorties
        ]);
    }
}

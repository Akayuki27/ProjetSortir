<?php

namespace App\Controller;


use App\Repository\VilleRepository;

use App\Entity\Campus;
use App\Form\CampusFiltreType;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Ville;
use App\Form\AjoutVilleType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin', name: 'app_admin')]
class AdminController extends AbstractController
{

    #[Route('/villes', name: '_villes' )]
    public function list(VilleRepository $villeRepository, Request $request, EntityManagerInterface $em): Response
    {
        $villes = $villeRepository->findAll();

        $ville = new Ville();
        $ajoutVille = $this->createForm(AjoutVilleType::class,$ville);
        $ajoutVille ->handleRequest($request);
        if($ajoutVille -> isSubmitted() && $ajoutVille->isValid())
        {
            $em ->persist($ville);
            $em ->flush();
            //$this ->addFlash('succes','Ville ajoutée avec succès');

            return $this->redirectToRoute('app_admin_villes', ['villes'=>$villes]);
        }

        return $this->render('admin/villes.html.twig', [
            'villes'=>$villes, 'ajoutVille'=>$ajoutVille
        ]);
    }

    #[Route('/campus', name: '_campus')]
    public function campus(Request $request, CampusRepository $campusRepository, EntityManagerInterface $entityManager): Response
    {
        $action = $request->query->get('action');

        switch ($action) {
            case 'supprimer':
                $campusId = $request->query->get('id');
                $campus = $campusRepository->find($campusId);
                if ($campus) {
                    $entityManager->remove($campus);
                    $entityManager->flush();
                }
                break;
            case 'ajouter':
                $nom = $request->request->get('nom');
                if ($nom) {
                    $campus = new Campus();
                    $campus->setNom($nom);
                    $entityManager->persist($campus);
                    $entityManager->flush();
                }
                break;
            default:
                // Afficher la liste des campus avec le formulaire de filtre
                $form = $this->createForm(CampusFiltreType::class);
                $form->handleRequest($request);

                $campuses = [];
                if ($form->isSubmitted() && $form->isValid()) {
                    $data = $form->getData();
                    $campuses = $campusRepository->rechercherParNom($data['nom']);
                } else {
                    $campuses = $campusRepository->findAll();
                }

                return $this->render('admin/campus.html.twig', [
                    'campuses' => $campuses,
                    'form' => $form->createView(),
                ]);
        }

        return $this->redirectToRoute('app_admin_campus');
    }

    #[Route('/campus/{id}/supprimer', name: '_campus_supprimer')]
    public function supprimerCampus(Request $request,Campus $campus, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($campus);
        $entityManager->flush();
        return $this->redirectToRoute('app_admin_campus');
    }

}

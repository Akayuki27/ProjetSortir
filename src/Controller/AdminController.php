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
    public function campus(CampusRepository $CampusRepository,EntityManagerInterface $entityManager): Response
    {
        $campuses = $CampusRepository->findAll();
        return $this->render('admin/campus.html.twig', ["campuses"=>$campuses]);
    }

    #[Route('/campus/{id}/supprimer', name: '_campus_supprimer')]
    public function supprimerCampus(Request $request,Campus $campus, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($campus);
        $entityManager->flush();
        return $this->redirectToRoute('app_admin_campus');
    }

    #[Route('/campus/ajouter', name: '_campus_ajouter')]
    public function ajouterCampus(Request $request, EntityManagerInterface $entityManager): Response
    {
        $nom = $request->request->get('nom');

        //nouvelle instance campus
        $campus = new Campus();
        $campus->setNom($nom);

        // récup, persist et plouf
        $entityManager->persist($campus);
        $entityManager->flush();

        return $this->redirectToRoute('app_admin_campus');
    }

    #[Route('/campus/filtre', name: '_campus_filtre')]
    public function filtreCampus(Request $request, EntityManagerInterface $entityManager): Response
    {
        //instance et soumission
        $form = $this->createForm(CampusFiltreType::class);
        $form->handleRequest($request);

        //validation
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            //filtre
            $campuses = $entityManager
                ->getRepository(Campus::class)
                ->rechercherParNom($data['nom']);

            return $this->render('campus.html.twig', [
                'campuses' => $campuses,
                'form' => $form->createView(), //passage du form a twig
            ]);
        }

        //si non valide
        return $this->render('campus.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}

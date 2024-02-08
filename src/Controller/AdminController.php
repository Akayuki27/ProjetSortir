<?php

namespace App\Controller;


use App\Entity\Participant;
use App\Form\ModifVilleType;
use App\Repository\ParticipantRepository;
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
use function PHPUnit\Framework\isEmpty;

#[Route('/admin', name: 'app_admin')]
class AdminController extends AbstractController
{

    #[Route('/villes', name: '_villes' )]
    public function list(VilleRepository $villeRepository, Request $request, EntityManagerInterface $em): Response
    {
        $ville = new Ville();
        $ajoutVille = $this->createForm(AjoutVilleType::class,$ville);
        $ajoutVille ->handleRequest($request);

        $action = $request->query->get('action');
        $villes = $villeRepository->findAll();

        if($ajoutVille -> isSubmitted() && $ajoutVille->isValid())
        {

            $em ->persist($ville);
            $em ->flush();
            //$this ->addFlash('succes','Ville ajoutée avec succès');
            return $this->redirectToRoute('app_admin_villes', [
                    'villes'=>$villes, 'ajoutVille'=>$ajoutVille

                ]
            );

        }

        if($action)
        {

            $data = $request->query->get('filtrer');
            if ($data) {
                $villes = $villeRepository->findByNom($data);
            }else {
                $villes = $villeRepository->findAll();
            }
        }


        return $this->render('admin/villes.html.twig', [
            'villes'=>$villes, 'ajoutVille'=>$ajoutVille

        ]
        );

    }

    #[Route('/villes/{id}/supprimer', name: '_villes_supprimer')]
    public function supprimerVilles(Request $request,Ville $ville, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($ville);
        $entityManager->flush();
        return $this->redirectToRoute('app_admin_villes');
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
            case 'modifier':
                $campusId = $request->query->get('id');
                $campus = $campusRepository->find($campusId);
                $nouveauNom = $request->request->get('nouveau_nom');
                if ($campus && $nouveauNom) {
                    $campus->setNom($nouveauNom);
                    $entityManager->persist($campus);
                    $entityManager->flush();
                }
                break;
            case 'filtrer':
                $nom = $request->query->get('filtrer');
                if ($nom) {
                    $campuses = $campusRepository->findByNom($nom);
                    dump($campuses);
                }else{
                    $campuses = $campusRepository->findAll();
                    dump($campuses);
                }
                break;
            default:
                $campuses = $campusRepository->findAll();
        }

        if(!empty($campuses)){
            return $this->render('admin/campus.html.twig', ['campuses' => $campuses]);
        }else{
            return $this->redirectToRoute('app_admin_campus');
        }
    }

    #[Route('/campus/{id}/supprimer', name: '_campus_supprimer')]
    public function supprimerCampus(Request $request,Campus $campus, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($campus);
        $entityManager->flush();
        return $this->redirectToRoute('app_admin_campus');
    }

    #[Route('/villes/modifier/{id}', name: '_villes_modifier' )]
    public function mofifierVille(EntityManagerInterface $entityManager,
                                  int $id,
                                  VilleRepository $villeRepository,
                                    Request $request) {
        $ville = $villeRepository->find($id);
        $form = $this->createForm(ModifVilleType::class, $ville);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Modification effectuée!');
            return $this->redirectToRoute('app_admin_villes');
        }
        return $this->render('admin/modifVille.html.twig', [
        'form' => $form, 'ville' => $ville]);
    }

    #[Route('/listeParticipant', name: '_participant_liste')]
    public function listeParticipant(Request $request,ParticipantRepository $pr, EntityManagerInterface $entityManager): Response
    {
        $participants = $pr->findAll();
        return $this->render('admin/listeParticipant.html.twig', ['participants' => $participants]);
    }

    #[Route('/participant/{id}/supprimer', name: '_participant_supprimer')]
    public function supprimerParticipant(Request $request,Participant $participant, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($participant);
        $entityManager->flush();
        return $this->redirectToRoute('app_admin_participant_liste');
    }
}

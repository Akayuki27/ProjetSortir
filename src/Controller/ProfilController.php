<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ModifProfilType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function PHPUnit\Framework\throwException;

#[Route('/profil', name: 'app_profil')]
class ProfilController extends AbstractController
{
    #[Route('/modification', name: '_modification')]
    public function register(Request $request,EntityManagerInterface $em): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $this->getUser();

        $pseudo = $this->getUser() ? $this->getUser()->getPseudo() : null;

        // Créer le formulaire en passant l'entité Participant
        $form = $this->createForm(ModifProfilType::class, $user, ['pseudo' => $pseudo]);

        // Traitement du formulaire soumis
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Enregistrer les modifications dans la base de données
            $em->persist($user);
            $em->flush();

            // Rediriger l'utilisateur vers une autre page après la modification
            return $this->redirectToRoute('app_main');
        }

        // Afficher le formulaire dans le template Twig
        return $this->render('profil/modification.html.twig', [
            'modificationProfil' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: '')]
    public function afficherProfil(Request $request,EntityManagerInterface $em, ParticipantRepository $participant, $id): Response
    {
       $participant = $participant->find($id);

       if (!$participant) {
           throw $this->createNotFoundException('Profile not found');
       }

        // Afficher le formulaire dans le template Twig
        return $this->render('profil/afficher.html.twig', [
            'participant' => $participant
        ]);
    }
}

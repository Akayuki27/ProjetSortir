<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ModifProfilType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/profil', name: 'app_profil')]
class ProfilController extends AbstractController
{
    #[Route('/modification/{id}', name: '_modification')]
    public function register(Request $request,int $id ,EntityManagerInterface $em, ParticipantRepository $pm): Response
    {
        // Récupérer l'utilisateur connecté
        $user = $pm->find($id);

        // Créer le formulaire en passant l'entité Participant
        $form = $this->createForm(ModifProfilType::class, $user, ['pseudo' => $user->getPseudo()]);
        // Traitement du formulaire soumis
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Gérer le téléchargement de la photo
            $photoFile = $form->get('imgName')->getData();
            if ($photoFile) {
                // Renommer et déplacer le fichier téléchargé
                $newFileName = md5(uniqid()).'.'.$photoFile->guessExtension();
                $photoFile->move($this->getParameter('photos'), $newFileName);

                // Mettre à jour le nom de fichier de la photo dans l'entité Participant
                $user->setImgName($newFileName);
            }
            // Enregistrer les modifications dans la base de données
            $em->flush();

            // Rediriger l'utilisateur vers une autre page après la modification
            $this->addFlash('success', 'Profil modifié! Good job!');
            return $this->redirectToRoute('sortie_list');
        }
        return $this->render('profil/modification.html.twig', [
            'modificationProfil' => $form,
        ]);
    }

    #[Route('/{id}', name: '')]
    public function afficherProfil(ParticipantRepository $participantRepository, $id): Response
    {
        $participant = $participantRepository->find($id);

        if (!$participant) {
            throw $this->createNotFoundException('Profile not found');
        }

        // Afficher le formulaire dans le template Twig
        return $this->render('profil/afficher.html.twig', [
            'participant' => $participant
        ]);
    }
}

<?php

namespace App\Controller;

use App\Entity\Group;
use App\Entity\Participant;
use App\Form\GroupType;
use App\Repository\GroupRepository;
use App\Repository\ParticipantRepository;
use Couchbase\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/groupe', name: 'app_groupe')]
class GroupeController extends AbstractController
{
    #[Route('/', name: '')]
    public function index(): Response
    {
        return $this->render('groupe/index.html.twig', [
            'controller_name' => 'GroupeController',
        ]);
    }

    #[Route('/list', name: '_list')]
    public function list(GroupRepository $groupRepository, ParticipantRepository $participantRepository): Response
    {
        $user = $this->getUser();
        $participant = $participantRepository->findOneBy(['pseudo' => $user->getUserIdentifier()]);

        // Vérifier si le participant existe
        if (!$participant) {
            // Gérer le cas où le participant n'est pas trouvé
        }

        // Récupérer les groupes associés au participant
        $groupes = $groupRepository->findByParticipant($participant);

        return $this->render('groupes/index.html.twig', [
            'groupes' => $groupes,
        ]);
    }


    #[Route('/create', name: '_create')]
    public function create(Request $request, ParticipantRepository $repo, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $participant = $repo->findOneBy(['pseudo' => $user->getUserIdentifier()]);
        $group = new Group();
        $form = $this->createForm(GroupType::class, $group);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $group->addParticipant($participant);
            //sauvegarde et redirection
            $entityManager->persist($group);
            $entityManager->flush();
            $this->addFlash('success', 'Groupe ajoutée! Good job!');
            return $this->redirectToRoute('app_groupe_list');
        }


        return $this->render('groupes/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/details/{id}', name: '_details')]
    public function details(Group $group): Response
    {
        return $this->render('groupes/detail.html.twig', [
            'group' => $group,
        ]);
    }
}

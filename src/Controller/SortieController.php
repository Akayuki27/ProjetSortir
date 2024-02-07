<?php

namespace App\Controller;

use App\Data\SearchData;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Form\ModifSortieType;
use App\Form\SearchForm;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\ParticipantRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\This;
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

            //********** debut du merdier **********//
            $lieu = null;
            $formData = $form->getData();

            // Vérifie si l'option "Nouveau lieu" a été sélectionnée et si un nouveau lieu a été saisi
            if ($formData['lieu'] === 'new' && !empty($formData['newLieu'])) {
                $lieu = new Lieu();
                $lieu->setNom($formData['newLieu']);
                $entityManager->persist($lieu);
            } elseif ($formData['Lieu']) {
                // Si un lieu existant a été sélectionné, récupérez-le
                $lieu = $formData['Lieu'];
            }

            if ($lieu) {
                // Associez le lieu à la sortie
                $sortie->setLieu($lieu);
            }
            //********** fin du merdier **********//

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
    public function list(SortieRepository $repository, Request $request,
    ParticipantRepository $repo): Response
    {
        $user = $this->getUser();
        $id = $repo->findOneBy(['pseudo' => $user->getUserIdentifier()])->getId();
        $data = new SearchData();
        $form = $this->createForm(SearchForm::class, $data);
        $form->handleRequest($request);
        $sorties = $repository->findSearch($data, $id);
        return $this->render('sortie/list.html.twig', [
        'sorties' => $sorties,
            'form' => $form
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

    #[Route('/inscription/{id}', name: 'inscription')]
    public function inscription(SortieRepository $repository, EntityManagerInterface $entityManager, int $id): Response
    {
        // Récupérer la sortie à laquelle l'utilisateur souhaite s'inscrire
        $sortie = $repository->find($id);
        $user = $this->getUser();
        $aujourdhui = new \DateTime();

        if (!$sortie) {
            throw $this->createNotFoundException('La sortie demandée n\'existe pas');
        }

        if ($sortie->getParticipants()->contains($user)) {
            $this->addFlash('danger', 'Vous êtes déjà inscrit à cette sortie !' );
            return $this->redirectToRoute('sortie_details', ['id'=>$sortie->getId()]);
        }

        if ($sortie->getNbInscriptionMax() <= $sortie->getParticipants()->count()) {
            $this->addFlash('danger', 'Désolé le nombre maximum de participants a été atteint !');
            return $this->redirectToRoute('sortie_details', ['id'=>$sortie->getId()]);
        }

        if ($sortie->getDateLimiteInscription() <= $aujourdhui ) {
            $this->addFlash('danger', 'Désolé mais la date d\'inscription à été dépassée');
            return $this->redirectToRoute('sortie_list');
        }


        // Ajouter l'utilisateur à la liste des participants de la sortie
        $sortie->addParticipant($user);

        // Enregistrer les modifications dans la base de données
        $entityManager->flush();

        $this->addFlash('success', 'Vous avez été inscrit à cette sortie avec succès !');
        return $this->redirectToRoute('sortie_details', ['id'=>$sortie->getId()]);
    }


    #[Route('/modifier/{id}', name: 'modifier')]
    public function modifier(SortieRepository $repository,
                             int $id,
                             Request $request,
                            EntityManagerInterface $entityManager): Response
    {
        $sortie = $repository->find($id);
        $form = $this->createForm(ModifSortieType::class, $sortie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Modification effectuée!');
            return $this->redirectToRoute('sortie_list');
        }
        return $this->render('sortie/modifier.html.twig', [
            'sortie' => $sortie,
            'form' => $form

        ]);
    }

    #[Route('/delete/{id}', name: 'delete')]
    public function delete(SortieRepository $repository,
                             int $id,
                             EntityManagerInterface $entityManager): Response
    {
        $sortie = $repository->find($id);
        $entityManager->remove($sortie);
        $entityManager->flush();
        $this->addFlash('success', 'Sortie supprimée!');
        return $this->redirectToRoute('sortie_list');
    }

    #[Route('/annuler/{id}', name: 'annuler')]
    public function annuler(SortieRepository $repository,
                             EtatRepository $etatRepository,
                             int $id,
                             Request $request,
                             EntityManagerInterface $entityManager): Response
    {
        $sortie = $repository->find($id);
        $form = $this->createForm(ModifSortieType::class, $sortie);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $sortie->setEtat($etatRepository->findOneBy(['libelle' => 'annulée']));
            $entityManager->flush();
            $this->addFlash('success', 'Sortie annulée!');
            return $this->redirectToRoute('sortie_list');
        }
        return $this->render('sortie/annuler.html.twig', [
            'sortie' => $sortie,
            'form' => $form

        ]);
    }

    #[Route('/desister/{id}', name: 'desister')]
    public function desister(SortieRepository $repository, EntityManagerInterface $entityManager, int $id): Response
    {
        // Récupérer la sortie à laquelle l'utilisateur souhaite s'inscrire
        $sortie = $repository->find($id);
        $user = $this->getUser();

        if (!$sortie) {
            throw $this->createNotFoundException('La sortie demandée n\'existe pas');
        }

        if ($sortie->getParticipants()->contains($user)) {
            // Retirer l'utilisateur de la liste des participants de la sortie
            $sortie->removeParticipant($user);

            // Enregistrer les modifications dans la base de données
            $entityManager->flush();

            $this->addFlash('success', 'Vous avez été retiré de cette sortie avec succès !');
            return $this->redirectToRoute('sortie_details', ['id'=>$sortie->getId()]);
        }
        // Enregistrer les modifications dans la base de données
        $entityManager->flush();

        $this->addFlash('success', 'Vous avez été enlevé de cette sortie avec succès !');
        return $this->redirectToRoute('sortie_details', ['id'=>$sortie->getId()]);
    }
}

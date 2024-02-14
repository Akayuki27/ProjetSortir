<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\CSVRegistrationType;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use function PHPUnit\Framework\isEmpty;

class RegistrationController extends AbstractController
{
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, Participant $participant): Response
    {
        $user = new Participant();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()

                )
            );

            $user->setActive(true);

            // Gérer le téléchargement de la photo
            $photoFile = $form->get('imgName')->getData();
            if ($photoFile) {
                // Renommer et déplacer le fichier téléchargé
                $newFileName = md5(uniqid()).'.'.$photoFile->guessExtension();
                $photoFile->move($this->getParameter('photos'), $newFileName);

                // Mettre à jour le nom de fichier de la photo dans l'entité Participant
                $user->setImgName($newFileName);
            }

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

            return $this->redirectToRoute('app_admin_participant_liste');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/register/csv', name: 'app_register_csv')]
    public function CSVRegister(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {

        $CSVform = $this->createForm(CSVRegistrationType::class);
        $CSVform->handleRequest($request);

        if ($CSVform->isSubmitted() && $CSVform->isValid()) {


            $formData = $CSVform->getData();
            $csv = $formData['csv'];
            $content = file_get_contents($csv);
            $content_array = array_filter(preg_split("/\r\n|\n|\r/", $content));
            $campus = $formData['estRattacheA'];


            foreach ($content_array as $line) {
                $data = explode(",", $line);


                            $participantCourant = new Participant();
                            $participantCourant->setActive(TRUE);
                            $participantCourant->setEstRattacheA($campus);
                            $participantCourant->setRoles(["ROLE_USER"]);
                            $participantCourant->setEmail($data[0]);
                            $participantCourant->setPseudo($data[1]);
                            $participantCourant->setFirstname($data[2]);
                            $participantCourant->setLastname($data[3]);
                            $participantCourant->setPhoneNumber($data[4]);
                            $participantCourant->setPassword(
                                $userPasswordHasher->hashPassword($participantCourant,$data[5])
                            );

                            $entityManager->persist($participantCourant);
                            $entityManager->flush();

            }


            return $this->redirectToRoute('app_admin_participant_liste');

        }


        return $this->render('registration/CSVRegister.html.twig', [
            'CSVform' => $CSVform,
        ]);
    }
}








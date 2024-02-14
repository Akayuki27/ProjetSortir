<?php

namespace App\DataFixtures;

use App\Entity\Sortie;
use App\Repository\CampusRepository;
use App\Repository\EtatRepository;
use App\Repository\LieuRepository;
use App\Repository\ParticipantRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class SortieFixtures extends Fixture
{
    private EtatRepository $etatRepository;
    private ParticipantRepository $participantRepository;
    private LieuRepository $lieuRepository;
    private CampusRepository $campusRepository;

    public function __construct(EtatRepository $etatRepository,
                                ParticipantRepository $participantRepository,
                                LieuRepository $lieuRepository,
                                CampusRepository $campusRepository)
    {
        $this->etatRepository = $etatRepository;
        $this->participantRepository = $participantRepository;
        $this->lieuRepository = $lieuRepository;
        $this->campusRepository = $campusRepository;
    }

    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create('fr_FR');

        $etats = $this->etatRepository->findAll();
        $particpants = $this->participantRepository->findAll();
        $lieux = $this->lieuRepository->findAll();
        $campuses = $this->campusRepository->findAll();


        for ($i = 0; $i<10; $i++) {
            $etatR = rand(0, count($etats) - 1);
            $particpantsR = rand(0, count($particpants) - 1);
            $lieuR = rand(0, count($lieux) - 1);
            $campusR = rand(0, count($campuses) - 2);


            $sortie = new Sortie();
            $sortie->setNom($faker->sentence(4));
            $sortie->setDateHeureDebut($faker->dateTimeBetween('+2 week','+4 week'));
            $sortie->setDuree($faker->numberBetween(15,600));
            $sortie->setDateLimiteInscription($faker->dateTimeBetween('+2 day','+1 week'));
            $sortie->setNbInscriptionMax($faker->numberBetween(2,50));
            $sortie->setInfosSortie($faker->paragraph(2));
            $sortie->setOrganisateur($particpants[$particpantsR]);
            $sortie->setCampus($campuses[$campusR]);
            $sortie->setEtat($etats[$etatR]);
            $sortie->setLieu($lieux[$lieuR]);


            $manager->persist($sortie);
        }
        $manager->flush();
    }
}

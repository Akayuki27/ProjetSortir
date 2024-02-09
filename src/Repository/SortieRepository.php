<?php

namespace App\Repository;

use App\Data\SearchData;
use App\Entity\Participant;
use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, private EntityManagerInterface $em, private EtatRepository $etatRepository)
    {
        parent::__construct($registry, Sortie::class);
    }

    /*
     * Récupérer les produits en lien avec une recherche
     * @return Sortie[]
     */
    public function findSearch(SearchData $search, int $id): array {
        $query = $this->createQueryBuilder('s')
            ->select('c', 'p', 's')
            ->join('s.campus', 'c')
            ->leftJoin('s.participants', 'p');

        if (!empty($search->q)) {
            $query = $query
                ->andWhere('s.nom LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }

        if (!empty($search->min)) {
            $query = $query
                ->andWhere('s.dateHeureDebut >= :min')
                ->setParameter('min', $search->min);
        }

        if (!empty($search->max)) {
            $query = $query
                ->andWhere('s.dateHeureDebut <= :max')
                ->setParameter('max', $search->max);
        }

        if (!empty($search->organisateur)) {
            $query = $query
                ->andWhere('s.organisateur = :organisateur')
                ->setParameter('organisateur', $id);
        }

        if ($search->inscrit) {
            $query = $query
                ->andWhere(':id MEMBER OF s.participants')
                ->setParameter('id', $id);
        }

        if ($search->nonInscrit) {
            $query = $query
                ->andWhere(':id NOT MEMBER OF s.participants')
                ->setParameter('id', $id);
        }

        if (!empty($search->passe)) {
            $query = $query
                ->andWhere('s.etat = :passe')
                ->setParameter('passe', 5);
        }

        if (!empty($search->campus)) {
            $query = $query
                ->andWhere('c.id = :campus')
                ->setParameter('campus', $search->campus->getId());
        }

        return $query->getQuery()->getResult();
    }


    /**
     * @return Sortie[] Returns an array of Sortie objects
     */
    public function findByDate(): array
    {
        $value = new \DateTime();
        return $this->createQueryBuilder('s')
            ->andWhere('s.dateHeureDebut < :val')
            ->setParameter('val', $value)
            ->getQuery()
           ->getResult()
        ;
    }

    public function updateEtat(): void
    {
        $sorties = $this->findByDate();
        foreach ($sorties as $sortie) {
            $sortie->setEtat($this->etatRepository->findOneBy(['libelle' => 'passée']));
            $this->em->flush();
        }
    }

    /**
     * @return Sortie[] Returns an array of Sortie objects
     */
    public function findByUser(Participant $user) {
        $query = $this->createQueryBuilder('s')
            ->select('c', 'p', 's')
            ->join('s.campus', 'c')
            ->leftJoin('s.participants', 'p')
            ->orWhere('c.id = :campus')
            ->setParameter('campus', $user->getEstRattacheA()->getId())
                ->orWhere('s.organisateur = :organisateur')
                ->setParameter('organisateur', $user->getId())
        ->orWhere(':id MEMBER OF s.participants')
            ->setParameter('id', $user->getId());
        return $query->getQuery()->getResult();
    }




    public function findByDateArchived(): array
    {
        $value = new \DateTime();
        $value->modify('- 1 month');

        return $this->createQueryBuilder('s')
            ->andWhere('s.dateHeureDebut < :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getResult()
            ;
    }

    public function ArchivedEtat(): void
    {
        $sorties = $this->findByDateArchived();
        foreach ($sorties as $sortie) {
            $sortie->setEtat($this->etatRepository->findOneBy(['libelle' => 'archivée']));
            $this->em->flush();
        }
    }



//    public function findOneBySomeField($value): ?Sortie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }


}
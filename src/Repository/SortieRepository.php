<?php

namespace App\Repository;

use App\Data\SearchData;
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
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    /*
     * Récupérer les produits en lien avec une recherche
     * @return Sortie[]
     */
    public function findSearch(SearchData $search): array {
        $query = $this
            ->createQueryBuilder('s')
        ->select('c', 's')
        ->join('s.campus', 'c');

        if (!empty($search->q)) {
            $query = $query
                ->andWhere('s.nom LIKE :q')
                ->setParameter('q', "%{$search->q}%");
        }

        if (!empty($search->min)) {
            $query= $query
                ->andWhere('s.dateHeureDebut >= :min')
                ->setParameter('min', $search->min);
        }

        if (!empty($search->max)) {
            $query= $query
                ->andWhere('s.dateHeureDebut <= :max')
                ->setParameter('max', $search->max);
        }

        if (!empty($search->organisateur)) {
            $query= $query
                ->andWhere('s.organisateur = :organisateur')
                ->setParameter('organisateur', $this->security->getUser());
        }

        if (!empty($search->inscrit)) {
            $query= $query
                ->andWhere('s.participants = :inscrit')
                ->setParameter('inscrit', $this->security->getUser());
        }

        if (!empty($search->nonInscrit)) {
            $query= $query
                ->andWhere('s.participants != :nonInscrit')
                ->setParameter('nonInscrit', $this->security->getUser());
        }

        if (!empty($search->passe)) {
            $query= $query
                ->andWhere('s.etat = :passe')
                ->setParameter('passe', 5);
        }

        if (!empty($search->campus)) {
            $query= $query
                ->andWhere('c.id IN (:campus)')
                ->setParameter('campus', $search->campus);
        }


        return $query->getQuery()->getResult();
    }

//    /**
//     * @return Sortie[] Returns an array of Sortie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

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

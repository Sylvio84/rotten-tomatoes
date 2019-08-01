<?php

namespace App\Repository;

use App\Entity\Movie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Movie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Movie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Movie[]    findAll()
 * @method Movie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MovieRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Movie::class);
    }

    public function search(string $query, ?int $max_results = 12)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.title LIKE :query')
            ->setParameter('query', '%' . $query . '%')
            ->orderBy('m.id', 'ASC')
            ->setMaxResults($max_results)
            ->getQuery()
            ->getResult();
    }

    public function findLast(int $limit = 100)
    {
        return $this->createQueryBuilder('m')
            ->addSelect('avg(r.notation) as movie_notation')
            ->addSelect('d')
            //->addSelect('c')
            ->leftJoin('m.ratings', 'r')
            ->leftJoin('m.director', 'd')
            //->leftJoin('m.categories', 'c')
            ->orderBy('m.releasedAt', 'DESC')
            ->groupBy('m.id')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult('movie_hydrator');
    }

    public function findBest(int $limit = 100)
    {
        return $this->createQueryBuilder('m')
            ->addSelect('avg(r.notation) as movie_notation')
            ->addSelect('d')
            //->addSelect('c')
            ->innerJoin('m.ratings', 'r')
            ->leftJoin('m.director', 'd')
            //->leftJoin('m.categories', 'c')
            ->orderBy('movie_notation', 'DESC')
            ->groupBy('m.id')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult('movie_hydrator');
    }
    public function findWorst(int $limit = 100)
    {
        return $this->createQueryBuilder('m')
            ->addSelect('avg(r.notation) as movie_notation')
            ->addSelect('d')
            //->addSelect('c')
            ->innerJoin('m.ratings', 'r')
            ->leftJoin('m.director', 'd')
            //->leftJoin('m.categories', 'c')
            ->orderBy('movie_notation', 'ASC')
            ->groupBy('m.id')
            //->having('movie_notation > 0')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult('movie_hydrator');
    }


    // /**
    //  * @return Movie[] Returns an array of Movie objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Movie
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}

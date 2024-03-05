<?php

namespace App\Repository;

use App\Entity\BriefingApp;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BriefingApp>
 *
 * @method BriefingApp|null find($id, $lockMode = null, $lockVersion = null)
 * @method BriefingApp|null findOneBy(array $criteria, array $orderBy = null)
 * @method BriefingApp[]    findAll()
 * @method BriefingApp[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BriefingAppRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BriefingApp::class);
    }

    public function add(BriefingApp $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(BriefingApp $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllWithEmpresa(): array
    {
        return $this->createQueryBuilder('ba')
            ->leftJoin('ba.usuario', 'u')
            ->leftJoin('u.empresa', 'e')
            ->addSelect('u')
            ->addSelect('e')
            ->getQuery()
            ->getResult();
    }

    public function findAllWithEmpresaAndUser()
    {
        return $this->createQueryBuilder('ba')
            ->leftJoin('ba.usuario', 'u')
            ->leftJoin('u.empresa', 'e')
            ->getQuery()
            ->getResult();
    }

         // Método para encontrar los últimos N registros
         public function findLastN($limit): array
         {
             return $this->createQueryBuilder('b')
                 ->orderBy('b.fecha_creacion_briefing_app', 'DESC')
                 ->setMaxResults($limit)
                 ->getQuery()
                 ->getResult();
         }

//    /**
//     * @return BriefingApp[] Returns an array of BriefingApp objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?BriefingApp
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

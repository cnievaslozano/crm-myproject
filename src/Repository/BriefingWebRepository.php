<?php

namespace App\Repository;

use App\Entity\BriefingWeb;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BriefingWeb>
 *
 * @method BriefingWeb|null find($id, $lockMode = null, $lockVersion = null)
 * @method BriefingWeb|null findOneBy(array $criteria, array $orderBy = null)
 * @method BriefingWeb[]    findAll()
 * @method BriefingWeb[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BriefingWebRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BriefingWeb::class);
    }

    public function add(BriefingWeb $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(BriefingWeb $entity, bool $flush = false): void
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

//    /**
//     * @return BriefingWeb[] Returns an array of BriefingWeb objects
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

//    public function findOneBySomeField($value): ?BriefingWeb
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

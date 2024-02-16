<?php

namespace App\Repository;

use App\Entity\BriefingLogo;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<BriefingLogo>
 *
 * @method BriefingLogo|null find($id, $lockMode = null, $lockVersion = null)
 * @method BriefingLogo|null findOneBy(array $criteria, array $orderBy = null)
 * @method BriefingLogo[]    findAll()
 * @method BriefingLogo[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BriefingLogoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BriefingLogo::class);
    }

    public function add(BriefingLogo $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(BriefingLogo $entity, bool $flush = false): void
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
//     * @return BriefingLogo[] Returns an array of BriefingLogo objects
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

//    public function findOneBySomeField($value): ?BriefingLogo
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

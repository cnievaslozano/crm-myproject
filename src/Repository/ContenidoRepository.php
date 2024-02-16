<?php

namespace App\Repository;

use App\Entity\Contenido;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Contenido>
 *
 * @method Contenido|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contenido|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contenido[]    findAll()
 * @method Contenido[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContenidoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Contenido::class);
    }

    public function add(Contenido $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Contenido $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllWithBriefingWebEmpresaAndUser()
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.briefing_web', 'bw')
            ->leftJoin('bw.usuario', 'u')
            ->leftJoin('u.empresa', 'e')
            ->getQuery()
            ->getResult();
    }

    public function findUserByContenidoId($contenidoId)
    {
        return $this->createQueryBuilder('c')
            ->select('u')
            ->leftJoin('c.briefing_web', 'bw')
            ->leftJoin('bw.usuario', 'u')
            ->andWhere('c.id = :id')
            ->setParameter('id', $contenidoId)
            ->getQuery()
            ->getOneOrNullResult();
    }


    //    /**
    //     * @return Contenido[] Returns an array of Contenido objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Contenido
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

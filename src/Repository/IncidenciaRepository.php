<?php

namespace App\Repository;

use App\Entity\Incidencia;
use App\Entity\BriefingWeb;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Incidencia>
 *
 * @method Incidencia|null find($id, $lockMode = null, $lockVersion = null)
 * @method Incidencia|null findOneBy(array $criteria, array $orderBy = null)
 * @method Incidencia[]    findAll()
 * @method Incidencia[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class IncidenciaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Incidencia::class);
    }

    public function add(Incidencia $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Incidencia $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    /**
     * Encuentra todas las incidencias asociadas a un briefing web.
     *
     * @param BriefingWeb $briefingWeb
     * @return Incidencia[] Devuelve un arreglo de incidencias
     */
    public function findByBriefingWeb(BriefingWeb $briefingWeb): array
    {
        return $this->createQueryBuilder('i')
            ->andWhere('i.briefing_web = :briefingWeb')
            ->setParameter('briefingWeb', $briefingWeb)
            ->getQuery()
            ->getResult();
    }

    // Método para encontrar los últimos N registros
    public function findLastN($limit): array
    {
        return $this->createQueryBuilder('b')
            ->orderBy('b.fecha_creacion_incidencia', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    //    /**
    //     * @return Incidencia[] Returns an array of Incidencia objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('i.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Incidencia
    //    {
    //        return $this->createQueryBuilder('i')
    //            ->andWhere('i.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

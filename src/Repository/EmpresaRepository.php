<?php

namespace App\Repository;

use App\Entity\Empresa;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Empresa>
 *
 * @method Empresa|null find($id, $lockMode = null, $lockVersion = null)
 * @method Empresa|null findOneBy(array $criteria, array $orderBy = null)
 * @method Empresa[]    findAll()
 * @method Empresa[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EmpresaRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Empresa::class);
    }

    public function add(Empresa $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Empresa $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * Obtiene todos los briefings asociados a una empresa.
     *
     * @param int $empresaId El ID de la empresa
     * @return array|null Los briefings asociados a la empresa, o null si no se encuentran
     */
    public function findBriefingsByEmpresaId(int $empresaId): ?array
    {
        return $this->createQueryBuilder('e')
            ->select('b')
            ->join('e.usuarios', 'u')
            ->leftJoin('u.briefingWeb', 'bWeb')
            ->leftJoin('u.briefingLogo', 'bLogo')
            ->leftJoin('u.briefingApp', 'bApp')
            ->where('e.id = :empresaId')
            ->setParameter('empresaId', $empresaId)
            ->getQuery()
            ->getResult();
    }

//    /**
//     * @return Empresa[] Returns an array of Empresa objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('e.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Empresa
//    {
//        return $this->createQueryBuilder('e')
//            ->andWhere('e.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}

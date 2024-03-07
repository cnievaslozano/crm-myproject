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
     * Obtiene el briefing web asociados a una empresa.
     *
     * @param int $empresaId El ID de la empresa
     * @return array|null El briefing web asociado a la empresa, o null si no se encuentran
     */

        public function findBriefingWebByEmpresaId(int $empresaId): ?bool
    {
        $empresa = $this->createQueryBuilder('e')
            ->leftJoin('e.usuarios', 'u')
            ->where('e.id = :empresaId')
            ->setParameter('empresaId', $empresaId)
            ->getQuery()
            ->getOneOrNullResult();

        if (!$empresa) {
            // Manejar el caso en que no se encuentra la empresa
            return null;
        }

        // Verificar si hay usuarios asociados a la empresa
        if ($empresa->getUsuarios()->isEmpty()) {
            // No hay usuarios asociados, se debe crear al menos uno antes de crear un briefing web
            return null;
        }

        // Existe al menos un usuario asociado, se puede continuar con la búsqueda de briefing web
        $briefingWeb = $empresa->getUsuarios()->first()->getBriefingWeb();

        return $briefingWeb ? true : null;
    }



    /**
     * Obtiene el briefing app asociados a una empresa.
     *
     * @param int $empresaId El ID de la empresa
     * @return array|null El briefing app asociado a la empresa, o null si no se encuentran
     */
    public function findBriefingAppByEmpresaId(int $empresaId): ?array
    {
        return $this->createQueryBuilder('e')
            ->select('b')
            ->join('e.usuarios', 'u')
            ->leftJoin('u.briefingApp', 'bApp')
            ->where('e.id = :empresaId')
            ->setParameter('empresaId', $empresaId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Obtiene el briefing Logo asociados a una empresa.
     *
     * @param int $empresaId El ID de la empresa
     * @return array|null El briefing logo asociado a la empresa, o null si no se encuentran
     */
    public function findBriefingLogoByEmpresaId(int $empresaId): ?array
    {
        return $this->createQueryBuilder('e')
            ->select('b')
            ->join('e.usuarios', 'u')
            ->leftJoin('u.briefingLogo', 'bLogo')
            ->where('e.id = :empresaId')
            ->setParameter('empresaId', $empresaId)
            ->getQuery()
            ->getResult();
    }

    // Método para encontrar los últimos N registros
    public function findLastN($limit): array
    {
        return $this->createQueryBuilder('b')
            ->orderBy('b.fecha_creacion_empresa', 'DESC')
            ->setMaxResults($limit)
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

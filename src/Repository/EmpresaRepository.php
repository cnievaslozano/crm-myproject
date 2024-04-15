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
     * Busca empresas por su nombre.
     *
     * @param string $nombre El nombre a buscar
     * @return Empresa[] Las empresas encontradas
     */
    public function buscarPorNombre(string $nombre): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.nombre LIKE :nombre')
            ->setParameter('nombre', '%' . $nombre . '%')
            ->getQuery()
            ->getResult();
    }

    /**
     * Busca empresas por su código.
     *
     * @param string $code
     * @return Empresa[] Returns an array of Empresa objects
     */
    public function buscarPorCodigo(string $code): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.code LIKE :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getResult();
    }

    /**
     * Encuentra una entidad por su ID.
     *
     * @param int $id El ID de la entidad que deseas buscar.
     * @return Empresa|null La Empresa encontrada o null si no se encontró ninguna.
     */
    public function buscarPorId(int $id): ?Empresa
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
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

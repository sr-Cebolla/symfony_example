<?php

namespace App\Repository;

use App\Entity\Usuario;
use App\Utils\Functions;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Usuario>
 *
 * @method Usuario|null find($id, $lockMode = null, $lockVersion = null)
 * @method Usuario|null findOneBy(array $criteria, array $orderBy = null)
 * @method Usuario[]    findAll()
 * @method Usuario[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UsuarioRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Usuario::class);
    }
    
    public function findByLetraAWithPagination(int $currentPage, int $limit): Paginator{
        $query = $this->createQueryBuilder('u')->andWhere('u.nombre LIKE :letraA')
        ->setParameter('letraA', 'A%') // % indica cualquier cantidad de caracteres después de 'A'
        ->getQuery()
        ->getResult();
        $paginator = Functions::paginate($query,$currentPage,$limit);
        return $paginator;
    }
    public function findAllWithPagination(int $currentPage, int $limit): Paginator
    {
        // Creamos nuestra query
        $query = $this->createQueryBuilder('u')
            ->getQuery();

        // Creamos un paginator con la funcion paginate
        $paginator = Functions::paginate($query, $currentPage, $limit);
        return $paginator;
    }

    /**Hacer una nueva query en el repositorio de Usuario 
     * para solo obtener los usuarios en donde su nombre comience con la letra A. */
    //    /**
    //     * @return Usuario[] Returns an array of Usuario objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Usuario
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}

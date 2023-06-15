<?php

namespace App\Repository;

use App\Entity\Dish;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Dish>
 *
 * @method Dish|null find($id, $lockMode = null, $lockVersion = null)
 * @method Dish|null findOneBy(array $criteria, array $orderBy = null)
 * @method Dish[]    findAll()
 * @method Dish[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DishRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dish::class);
    }

    public function save(Dish $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Dish $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function getDishQueryBuilder(): QueryBuilder
    {
        return $this->createQueryBuilder('dish');
    }

    public function filterPublishedDish($queryBuilder, $activeValue): QueryBuilder
    {
        return $queryBuilder
            ->andWhere('dish.isPublish = :activeValue')
            ->setParameter('activeValue', $activeValue);
    }

    public function addOrderByNameAsc($querybuilder): QueryBuilder
    {
        return $querybuilder
            ->orderBy('dish.name', 'ASC');
    }

    public function executeQuery($queryBuilder): array
    {
        return $queryBuilder
            ->getQuery()
            ->getResult();
    }

    public function getAllActiveDish(): array
    {
        $qb = $this->getDishQueryBuilder();
        $qb = $this->filterPublishedDish($qb, true);
        $qb = $this->addOrderByNameAsc($qb);
        return $this->executeQuery($qb);
    }
}

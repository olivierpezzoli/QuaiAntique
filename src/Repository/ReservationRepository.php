<?php

namespace App\Repository;


use App\Entity\Reservation;
use App\Entity\OpeningHours;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\QueryBuilder;
use Brick\DateTime\LocalDateTime;
use Brick\DateTime\LocalTime;


/**
 * @extends ServiceEntityRepository<Reservation>
 *
 * @method Reservation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Reservation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Reservation[]    findAll()
 * @method Reservation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ReservationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Reservation::class);
    }

    public function save(Reservation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Reservation $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function selectAll(): QueryBuilder {
        return $this->createQueryBuilder('r');
    }

    public function getByDateAndService(\DateTime $dateReservation, OpeningHours $oh) {
        $current = LocalDateTime::fromNativeDateTime($dateReservation);
        $date = $current->getDate();

        $lunchStart = new LocalDateTime($date, LocalTime::fromNativeDateTime($oh->getLunchStart()));
        $lunchEnd = new LocalDateTime($date, LocalTime::fromNativeDateTime($oh->getLunchEnd()));
        $eveningStart = new LocalDateTime($date, LocalTime::fromNativeDateTime($oh->getEveningStart()));
        $eveningEnd = new LocalDateTime($date, LocalTime::fromNativeDateTime($oh->getEveningEnd()));

        $reservationsQb = $this->selectAll();

        if ($current->isAfterOrEqualTo($lunchStart) && $current->isBefore($lunchEnd)) {
            $start_date = $lunchStart;
            $end_date = $lunchEnd;
        } elseif ($current->isAfterOrEqualTo($eveningStart) && $current->isBefore($eveningEnd)) {
            $start_date = $eveningStart;
            $end_date = $eveningEnd;
        } else {
             return [];
        }

        $reservationsQb->where("r.date BETWEEN :date_start AND :date_end")
            ->setParameter('date_start', $start_date->toNativeDateTime()->format('Y-m-d H:i:s'))
            ->setParameter('date_end', $end_date->toNativeDateTime()->format('Y-m-d H:i:s'));

        return $reservationsQb->getQuery()->getResult();
    }
}

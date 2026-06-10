<?php

namespace App\Repository;

use App\Entity\Event;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class EventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Event::class);
    }

    public function findPublished(array $filters = []): array
    {
        $qb = $this->createQueryBuilder('e')
            ->where('e.isPublished = true');

        if (!empty($filters['city'])) {
            $qb->andWhere('e.city = :city')
                ->setParameter('city', $filters['city']);
        }

        if (!empty($filters['propertyType'])) {
            $qb->andWhere('e.propertyType = :propertyType')
                ->setParameter('propertyType', $filters['propertyType']);
        }

        if (!empty($filters['offerType'])) {
            $qb->andWhere('e.offerType = :offerType')
                ->setParameter('offerType', $filters['offerType']);
        }

        if (!empty($filters['maxRent'])) {
            $qb->andWhere('e.monthlyRent <= :maxRent')
                ->setParameter('maxRent', (int) $filters['maxRent']);
        }

        return $qb->orderBy('e.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByOrganizer(int $organizerId): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.organizer = :id')
            ->setParameter('id', $organizerId)
            ->orderBy('e.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}

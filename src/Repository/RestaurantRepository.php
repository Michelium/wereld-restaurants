<?php

namespace App\Repository;

use App\Entity\Restaurant;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Restaurant>
 */
class RestaurantRepository extends ServiceEntityRepository {

    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Restaurant::class);
    }

    /* @return Restaurant[] */
    public function findAllWithCountry(): array {
        return $this->createQueryBuilder('r')
            ->where('r.country IS NOT NULL')
            ->orderBy('r.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /* @return Restaurant[] */
    public function findByCountries(array $countryCodes): array {
        return $this->createQueryBuilder('r')
            ->innerJoin('r.country', 'c')
            ->andWhere('c.code IN (:codes)')
            ->setParameter('codes', $countryCodes)
            ->orderBy('r.name', 'ASC')
            ->getQuery()
            ->getResult();
    }

}

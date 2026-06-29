<?php

namespace App\Repository;

use App\Entity\Review;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Review>
 */
class ReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Review::class);
    }


    /**
     * @return Review[]
     */
    // Return the newest reviews, optionally filtered by company name.
    public function findLatest(?string $search = null): array
    {
        $qb = $this->createQueryBuilder('r')
            ->orderBy('r.createdAt', 'DESC');

        if ($search !== null && trim($search) !== '') {
            $qb
                ->andWhere('LOWER(r.companyName) LIKE :search')
                ->setParameter('search', '%' . mb_strtolower(trim($search)) . '%');
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * @return array<int, array{
     *     companyName: string,
     *     reviewCount: int,
     *     averageRating: string
     * }>
     */
    // Group reviews by company and calculate average rating and review count.
    public function getCompanyStatistics(): array
    {
        return $this->createQueryBuilder('r')
            ->select('r.companyName AS companyName')
            ->addSelect('COUNT(r.id) AS reviewCount')
            ->addSelect('AVG(r.rating) AS averageRating')
            ->groupBy('r.companyName')
            ->orderBy('averageRating', 'DESC')
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @return Review[]
     */
    // Return all reviews for one company ordered by creation time.
    public function getReviewsByCompanyOrdered(string $companyName): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.companyName = :company')
            ->setParameter('company', $companyName)
            ->orderBy('r.createdAt', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Review[]
     */
    // Return the highest-rated reviews for a company, newest first.
    public function getLatestTopReviews(string $companyName, int $limit = 3): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.companyName = :company')
            ->setParameter('company', $companyName)
            ->orderBy('r.rating', 'DESC')
            ->addOrderBy('r.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return Review[]
     */
    // Return the lowest-rated reviews for a company, newest first.
    public function getLatestWorstReviews(string $companyName, int $limit = 3): array
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.companyName = :company')
            ->setParameter('company', $companyName)
            ->orderBy('r.rating', 'ASC')
            ->addOrderBy('r.createdAt', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }
}
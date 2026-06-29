<?php

namespace App\Tests\Unit;

use App\Entity\Review;
use App\Service\ReviewAnalytics;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class ReviewRepositoryTest extends TestCase
{
    public function testAverageRatingIsCalculatedCorrectly(): void
    {
        $service = new ReviewAnalytics();

        $reviews = [
            $this->createReview(5),
            $this->createReview(3),
            $this->createReview(4),
        ];

        $this->assertSame(4.0, $service->calculateAverageRating($reviews));
    }

    public function testReviewsAreSortedByCreationDateDescending(): void
    {
        $service = new ReviewAnalytics();

        $newer = $this->createReview(5, '2024-02-01 10:00:00');
        $older = $this->createReview(3, '2024-01-01 10:00:00');

        $sorted = $service->sortByNewestFirst([$older, $newer]);

        $this->assertSame([$newer, $older], $sorted);
    }

    private function createReview(int $rating, string $createdAt = '2024-01-01 10:00:00'): Review
    {
        $review = new Review();
        $review->setCompanyName('Acme');
        $review->setRating($rating);
        $review->setReviewText('Nice');
        $review->setAuthorEmail('user@example.com');
        $review->setCreatedAt(new DateTimeImmutable($createdAt));
        $review->setUpdatedAt(new DateTimeImmutable($createdAt));

        return $review;
    }
}

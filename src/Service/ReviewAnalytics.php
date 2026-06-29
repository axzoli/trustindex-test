<?php

namespace App\Service;

use App\Entity\Review;

final class ReviewAnalytics
{
    /**
     * @param Review[] $reviews
     */
    public function calculateAverageRating(array $reviews): float
    {
        if ($reviews === []) {
            return 0.0;
        }

        $total = 0;
        foreach ($reviews as $review) {
            $total += (int) $review->getRating();
        }

        return round($total / count($reviews), 1);
    }

    /**
     * @param Review[] $reviews
     * @return Review[]
     */
    public function sortByNewestFirst(array $reviews): array
    {
        usort($reviews, static function (Review $left, Review $right): int {
            return $right->getCreatedAt() <=> $left->getCreatedAt();
        });

        return $reviews;
    }
}

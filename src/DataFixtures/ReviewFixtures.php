<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Entity\Review;
use DateTimeImmutable;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class ReviewFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $companies = [
            'Google',
            'Facebook',
            'Trustpilot',
            'Amazon',
            'Apple',
            'Microsoft',
            'Trustindex',
        ];

        $reviewTexts = [
            'Nagyon elégedett vagyok a szolgáltatással, gyors és megbízható volt minden.',
            'Jó tapasztalatom volt, de néhány apróságon még lehetne javítani.',
            'Az ügyfélszolgálat segítőkész volt, hamar választ kaptam a kérdésemre.',
            'A szolgáltatás minősége rendben volt, összességében ajánlom másoknak is.',
            'Egyszerű használat, átlátható felület és korrekt kommunikáció.',
            'Volt egy kisebb problémám, de végül gyorsan megoldották.',
            'Kiemelkedő élmény volt, biztosan újra igénybe veszem.',
            'Átlagos tapasztalat, nem rossz, de nem is kiemelkedő.',
            'Megbízható cég, pontos teljesítés és korrekt hozzáállás.',
            'A folyamat gyors volt, a végeredménnyel elégedett vagyok.',
        ];

        for ($i = 1; $i <= 100; $i++) {
            $review = new Review();

            $companyName = $companies[($i - 1) % count($companies)];
            $rating = ($i % 5) + 1;
            $reviewText = $reviewTexts[($i - 1) % count($reviewTexts)];

            $daysAgo = ($i % 180) + 1;
            $hours = ($i % 24) + 8;
            $minutes = ($i * 7) % 60;

            $createdAt = (new DateTimeImmutable(sprintf('-%d days', $daysAgo)))
                ->setTime($hours, $minutes);

            $updatedAt = $createdAt->modify(sprintf('+%d hours', ($i % 12) + 1));

            $review
                ->setCompanyName($companyName)
                ->setRating($rating)
                ->setReviewText($reviewText)
                ->setAuthorEmail(sprintf('user%03d@example.com', $i))
                ->setCreatedAt($createdAt)
                ->setUpdatedAt($updatedAt);

            $manager->persist($review);
        }

        $manager->flush();
    }
}
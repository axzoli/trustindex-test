<?php

namespace App\Tests\Functional;

use App\Entity\Review;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

final class CompanyPageTest extends WebTestCase
{
    private EntityManagerInterface $entityManager;
    private $client;

    protected function setUp(): void
    {
        parent::setUp();

        // Set up a temporary SQLite database for testing.
        $dbPath = __DIR__ . '/../../var/test.db';
        if (file_exists($dbPath)) {
            unlink($dbPath);
        }

        // Set the DATABASE_URL environment variable to use SQLite for testing.
        putenv('DATABASE_URL=sqlite:///' . $dbPath);
        $_ENV['DATABASE_URL'] = 'sqlite:///' . $dbPath;
        $_SERVER['DATABASE_URL'] = 'sqlite:///' . $dbPath;

        // Ensure the kernel is shut down before creating a new client and entity manager.
        self::ensureKernelShutdown();
        $this->client = static::createClient();
        $this->entityManager = static::getContainer()->get(EntityManagerInterface::class);

        // Create the database schema for testing.
        $schemaTool = new SchemaTool($this->entityManager);
        $metadata = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool->createSchema($metadata);
    }

    public function testCompanyDetailsPageShowsChartsAndReviews(): void
    {
        $review = (new Review())
            ->setCompanyName('Acme')
            ->setRating(5)
            ->setReviewText('Excellent service')
            ->setAuthorEmail('user@example.com')
            ->setCreatedAt(new DateTimeImmutable('2024-01-01 10:00:00'))
            ->setUpdatedAt(new DateTimeImmutable('2024-01-01 10:00:00'));

        // Persist the review to the test database.
        $this->entityManager->persist($review);
        $this->entityManager->flush();

        // Request the company details page for "Acme".
        $this->client->request('GET', '/companies/Acme');

        // Assert that the response is successful and contains expected content.
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Acme');
        $this->assertSelectorTextContains('body', 'Top 3 vélemény');
        $this->assertSelectorTextContains('body', 'Leggyengébb 3 vélemény');
    }
}

<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Review;
use App\Form\ReviewType;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ReviewController extends AbstractController
{
    // List all reviews and optionally filter by company name.
    #[Route('/', name: 'review_index', methods: ['GET'])]
    public function index(Request $request, ReviewRepository $reviewRepository): Response
    {
        $search = $request->query->get('q');

        return $this->render('review/index.html.twig', [
            'reviews' => $reviewRepository->findLatest($search),
            'search' => $search,
        ]);
    }

    // Create a new review via the form and save it to the database.
    #[Route('/reviews/new', name: 'review_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($review);
            $entityManager->flush();

            $this->addFlash('success', 'Köszönjük a véleményed!');

            return $this->redirectToRoute('review_index');
        }

        return $this->render('review/new.html.twig', [
            'form' => $form,
        ]);
    }

    // Show one review in detail.
    #[Route('/reviews/{id}', name: 'review_show', methods: ['GET'])]
    public function show(Review $review): Response
    {
        return $this->render('review/show.html.twig', [
            'review' => $review,
        ]);
    }

    // Show a company overview page with aggregate statistics.
    #[Route('/companies', name: 'company_statistics', methods: ['GET'])]
    public function companies(ReviewRepository $reviewRepository): Response
    {
        return $this->render('company/index.html.twig', [
            'companies' => $reviewRepository->getCompanyStatistics(),
        ]);
    }

    // Show one company page with charts and the best/worst reviews.
    #[Route('/companies/{companyName}', name: 'company_show', methods: ['GET'], requirements: ['companyName' => ".+"])]
    public function companyShow(string $companyName, ReviewRepository $reviewRepository): Response
    {
        $reviews = $reviewRepository->getReviewsByCompanyOrdered($companyName);

        $chartLabels = [];
        $chartRatings = [];
        foreach ($reviews as $r) {
            $chartLabels[] = $r->getCreatedAt()->format('Y-m-d H:i');
            $chartRatings[] = (int) $r->getRating();
        }

        // aggregate counts per day
        $counts = [];
        foreach ($reviews as $r) {
            $day = $r->getCreatedAt()->format('Y-m-d');
            if (!isset($counts[$day])) {
                $counts[$day] = 0;
            }
            $counts[$day]++;
        }
        // sort by date ascending
        ksort($counts);
        $countLabels = array_keys($counts);
        $countData = array_values($counts);

        // Get the top 3 and worst 3 reviews for this company.
        $topReviews = $reviewRepository->getLatestTopReviews($companyName, 3);
        $worstReviews = $reviewRepository->getLatestWorstReviews($companyName, 3);

        return $this->render('company/show.html.twig', [
            'companyName' => $companyName,
            'chartLabels' => $chartLabels,
            'chartRatings' => $chartRatings,
            'countLabels' => $countLabels,
            'countData' => $countData,
            'topReviews' => $topReviews,
            'worstReviews' => $worstReviews,
        ]);
    }
}
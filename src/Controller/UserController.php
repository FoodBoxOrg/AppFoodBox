<?php

namespace App\Controller;

use App\Repository\ReviewRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
#[Route('/user')]
final class UserController extends AbstractController
{
    #[Route('/{id}', name: 'user_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(int $id): Response
    {
        return $this->json(['id' => $id]);
    }

    #[Route('/{id}/reviews', name: 'user_reviews', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function reviews(int $id, ReviewRepository $reviewRepository): Response
    {
        $reviews = $reviewRepository->findBy(['user' => $id]);
        $average = 0;
        for($i = 0; $i < count($reviews); $i++){
            $average += $reviews[$i]->getRate();
        }
        if (count($reviews) > 0){
            $average = $average / count($reviews);
        }
        return $this->render('user/reviews.html.twig', [
            'reviews' => $reviews,
            'average' => round($average,1),
            'reviews_count' => count($reviews),
        ]);
    }
}

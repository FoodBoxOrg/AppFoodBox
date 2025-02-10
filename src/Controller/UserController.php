<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ReviewRepository;
use App\Repository\UserRepository;
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
    public function reviews(int $id, ReviewRepository $reviewRepository, UserRepository $userRepository): Response
    {
        $user = $userRepository->find($id);
        $reviews = $reviewRepository->findBy(['user' => $id]);
        $average = 0;
        for($i = 0; $i < count($reviews); $i++){
            $average += $reviews[$i]->getRate();
        }
        if (count($reviews) > 0){
            $average = $average / count($reviews);
        }
        return $this->render('user/reviews.html.twig', [
            'user' => $user,
            'reviews' => $reviews,
            'average' => round($average,1),
            'reviews_count' => count($reviews),
        ]);
    }
}

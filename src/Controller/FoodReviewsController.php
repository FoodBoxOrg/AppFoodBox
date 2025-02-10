<?php

namespace App\Controller;

use App\Entity\Food;
use App\Entity\Review;
use App\Form\FoodReviewType;
use App\Repository\FoodRepository;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//test
#[Route('/review')]
class FoodReviewsController extends AbstractController
{
    /**
     * Affiche la liste de tous les Food avec un bouton "Noter".
     *
     * GET /foods
     */
    #[Route('/', name: 'food_review_list', methods: ['GET'])]
    public function list(FoodRepository $foodRepository): Response
    {
        $foods = $foodRepository->findAll();
        return $this->render('foods/list.html.twig', [
            'foods' => $foods,
        ]);
    }

    #[Route('/{id}', name: 'food_review_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(int $id, FoodRepository $foodRepository, ReviewRepository $reviewRepository): Response
    {
        $food = $foodRepository->find($id);
        $reviews = $reviewRepository->findBy(['food' => $food]);
        $average = 0;
        for($i = 0; $i < count($reviews); $i++){
            $average += $reviews[$i]->getRate();
        }
        if (count($reviews) > 0){
            $average = $average / count($reviews);
        }

        if (!$food) {
            throw $this->createNotFoundException('Food not found');
        }

        return $this->render('food_reviews/show.html.twig', [
            'food' => $food,
            'reviews' => $reviews,
            'average' => $average,
            'reviews_count' => count($reviews),
        ]);
    }

    /**
     * Affiche un formulaire pour noter un Food (0..5 + commentaire).
     * Si l'ID du food n'existe pas, on redirige vers la page de création de Food.
     *
     * GET|POST /foods/{id}/review
     */
    #[Route('/{id}/rate', name: 'food_review', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function review(
        int $id,
        FoodRepository $foodRepository,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $food = $foodRepository->find($id);

        if (!$food) {
            return $this->redirectToRoute('food_create');
        }

        if ($request->isMethod('POST')) {
            $rate     = (int) $request->request->get('rate', 0);
            $comment  = $request->request->get('comment', null);

            if ($rate < 0) {
                $rate = 0;
            } elseif ($rate > 5) {
                $rate = 5;
            }

            $review = new Review();
            $review->setFood($food);
            $review->setUser($this->getUser());
            $review->setRate($rate);
            $review->setComment($comment);

            $entityManager->persist($review);
            $entityManager->flush();

            $this->addFlash('success', 'Votre review a bien été enregistrée !');

            return $this->redirectToRoute('food_review_list');
        }

        return $this->render('food_reviews/food_review.html.twig', [
            'food' => $food,
        ]);
    }

    #[Route('/review/create', name: 'review_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $review = new Review();

        $review->setUser($this->getUser());

        $form = $this->createForm(FoodReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($review);
            $em->flush();

            $this->addFlash('success', 'Review créée avec succès !');

            return $this->redirectToRoute('homepage');
        }

        return $this->render('review/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

}

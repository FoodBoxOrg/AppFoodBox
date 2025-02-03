<?php

namespace App\Controller;

use App\Entity\Food;
use App\Entity\FoodTags;
use App\Entity\Tags;
use App\Form\FoodsType;
use App\Repository\FoodRepository;
use App\Repository\TagsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/foods')]
final class FoodsController extends AbstractController
{
    /**
     * Récupère tous les foods et renvoie un JSON.
     *
     * Exemple d'appel : GET /api/foods/
     */
    #[Route('/', name: 'food_list', methods: ['GET'])]
    public function list(FoodRepository $foodRepository): Response
    {
        $foods = $foodRepository->findAll();
        $data = [];

        foreach ($foods as $food) {
            $tags = [];
            foreach ($food->getFoodTags() as $foodTag) {
                $tags[] = $foodTag->getTag()->getName();
            }

            $data[] = [
                'id'       => $food->getId(),
                'name'     => $food->getName(),
                'origin'   => $food->getOrigin(),
                'imageId'  => $food->getImageId(),
                'tags'     => $tags,
            ];
        }

        return $this->json($data);
    }

    /**
     * Récupère un food par son id et renvoie un JSON.
     *
     * Exemple d'appel : GET /api/foods/42
     */
    #[Route('/{id}', name: 'food_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(int $id, FoodRepository $foodRepository): Response
    {
        $food = $foodRepository->find($id);

        if (!$food) {
            return $this->json(['message' => 'Food non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $tags = [];
        foreach ($food->getFoodTags() as $foodTag) {
            $tags[] = $foodTag->getTag()->getName();
        }

        $data = [
            'id'       => $food->getId(),
            'name'     => $food->getName(),
            'origin'   => $food->getOrigin(),
            'imageId'  => $food->getImageId(),
            'tags'     => $tags,
            'averageRating' => $food->getAverageRating() ?? 'Pas de note',
            'reviews' => $food->getComment()
        ];

        return $this->json($data);
    }

    /**
     * Crée un nouveau Food.
     *
     * Exemple d'appel : POST /foods/create
     * Body JSON :
     * {
     *   "name": "Pizza",
     *   "origin": "Italie",
     *   "imageId": "pizza.jpg",
     *   "tags": ["Italien", "FastFood"]
     * }
     */
    #[Route('/create', name: 'food_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $food = new Food();
        $form = $this->createForm(FoodsType::class, $food);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($food);
            $em->flush();

            $this->addFlash('success', 'Food créé avec succès !');
            return $this->redirectToRoute('food_list');
        }

        return $this->render('foods/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    /**
     * Récupère tous les foods ayant un tag donné et renvoie un JSON.
     *
     * Exemple d'appel : GET /foods/tag/Italien
     */
    #[Route('/tag/{tagName}', name: 'food_by_tag', methods: ['GET'])]
    public function listByTag(string $tagName, TagsRepository $tagsRepository): Response
    {
        $tag = $tagsRepository->findOneBy(['name' => $tagName]);

        if (!$tag) {
            return $this->json(['message' => 'Tag non trouvé'], Response::HTTP_NOT_FOUND);
        }

        $foodsData = [];
        foreach ($tag->getFoodTags() as $foodTag) {
            $food = $foodTag->getFood();
            $foodsData[] = [
                'id'      => $food->getId(),
                'name'    => $food->getName(),
                'origin'  => $food->getOrigin(),
                'imageId' => $food->getImageId(),
            ];
        }

        return $this->json([
            'tag'   => $tagName,
            'foods' => $foodsData,
        ]);
    }
}

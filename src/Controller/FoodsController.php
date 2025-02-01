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
     * Récupère tous les foods et les affiche via Twig.
     *
     * Exemple d'appel : GET /foods/
     */
    #[Route('/', name: 'food_list', methods: ['GET'])]
    public function list(FoodRepository $foodRepository): Response
    {
        $foods = $foodRepository->findAll();
        $data = [];

        foreach ($foods as $food) {
            // On parcourt les FoodTags pour récupérer le nom de chaque tag
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

        return $this->render('foods/list.html.twig', [
            'foods' => $data,
        ]);
    }

    /**
     * Récupère un food en particulier par son id et l'affiche via Twig.
     *
     * Exemple d'appel : GET /foods/42
     */
    #[Route('/{id}', name: 'food_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(int $id, FoodRepository $foodRepository): Response
    {
        $food = $foodRepository->find($id);

        if (!$food) {
            throw $this->createNotFoundException('Food non trouvé');
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
        ];

        return $this->render('foods/show.html.twig', [
            'food' => $data,
        ]);
    }

    /**
     * Crée un nouveau Food.
     *
     * - GET  /foods/create : affiche le formulaire de création.
     * - POST /foods/create : traite la soumission du formulaire.
     *
     * Exemple d'appel (soumission du formulaire) :
     *   - Les données sont récupérées depuis $request->request (type application/x-www-form-urlencoded ou multipart/form-data).
     */
    #[Route('/create', name: 'food_create', methods: ['GET', 'POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        TagsRepository $tagsRepository
    ): Response {
        $food = new Food();
        $form = $this->createForm(FoodsType::class, $food);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Les champs "name", "origin" et "imageId" ont déjà été affectés à l'objet $food
            // Récupération de la valeur du champ non mappé "tags"
            $tagsInput = $form->get('tags')->getData();

            // Persistance du Food
            $em->persist($food);

            // Traitement des tags s'il y a une saisie
            if (!empty($tagsInput)) {
                // On suppose que les tags sont saisis sous forme d'une chaîne séparée par des virgules (ex: "FastFood,Italien")
                $tagNames = array_map('trim', explode(',', $tagsInput));
                foreach ($tagNames as $tagName) {
                    if (empty($tagName)) {
                        continue;
                    }
                    // Recherche d'un tag existant
                    $tag = $tagsRepository->findOneBy(['name' => $tagName]);
                    if (!$tag) {
                        // Création du tag s'il n'existe pas
                        $tag = new Tags();
                        $tag->setName($tagName);
                        $em->persist($tag);
                    }

                    // Création de la relation entre Food et Tag via FoodTags
                    $foodTag = new FoodTags();
                    $foodTag->setFood($food);
                    $foodTag->setTag($tag);
                    $em->persist($foodTag);
                }
            }

            $em->flush();
            $this->addFlash('success', 'Food créé avec succès');

            return $this->redirectToRoute('food_list');
        }

        return $this->render('foods/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Récupère tous les foods ayant un tag donné et les affiche via Twig.
     *
     * Exemple d'appel : GET /foods/tag/Italien
     */
    #[Route('/tag/{tagName}', name: 'food_by_tag', methods: ['GET'])]
    public function listByTag(string $tagName, TagsRepository $tagsRepository): Response
    {
        $tag = $tagsRepository->findOneBy(['name' => $tagName]);

        if (!$tag) {
            throw $this->createNotFoundException('Tag non trouvé');
        }

        $foods = [];
        foreach ($tag->getFoodTags() as $foodTag) {
            $food = $foodTag->getFood();
            $foods[] = [
                'id'      => $food->getId(),
                'name'    => $food->getName(),
                'origin'  => $food->getOrigin(),
                'imageId' => $food->getImageId(),
            ];
        }

        return $this->render('foods/list_by_tag.html.twig', [
            'tagName' => $tagName,
            'foods'   => $foods,
        ]);
    }
}

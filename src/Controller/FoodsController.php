<?php

namespace App\Controller;

use App\Entity\Food;
use App\Entity\FoodTags;
use App\Entity\Tags;
use App\Form\EditFoodsType;
use App\Form\FoodsType;
use App\Repository\FoodRepository;
use App\Repository\FoodTagsRepository;
use App\Repository\TagsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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
     * - GET /foods/create : affiche le formulaire de création.
     * - POST /foods/create : traite la soumission du formulaire.
     *
     * Exemple d'appel (soumission du formulaire) –
     * * Les données sont récupérées depuis $request→request (type application/x-www-form-urlencoded ou multipart/form-data).
     */
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour accéder à cette page')]
    #[Route('/create', name: 'food_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response {
        $food = new Food();
        $form = $this->createForm(FoodsType::class, $food);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persistance du Food
            $em->persist($food);

            $em->flush();
            $this->addFlash('success', 'Food créé avec succès');

            return $this->redirectToRoute('food_list');
        }

        return $this->render('foods/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Modifie un food existant.
     *
     * - GET /foods/42/edit : affiche le formulaire de modification.
     * - POST /foods/42/edit : traite la soumission du formulaire.
     *
     * Exemple d'appel (soumission du formulaire) –
     * * Les données sont récupérées depuis $request→request (type application/x-www-form-urlencoded ou multipart/form-data).
     */
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour accéder à cette page')]
    #[Route('/{id}/edit', name: 'food_edit', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function edit(int $id, Request $request, EntityManagerInterface $em, FoodRepository $foodRepository,): Response {

        $food = $foodRepository->find($id);
        if (!$food) {
            throw $this->createNotFoundException('Food non trouvé');
        }

        $form = $this->createForm(EditFoodsType::class, $food);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persistance du Food
            $em->persist($food);

            $em->flush();
            $this->addFlash('success',  $food->getName().' modifié avec succès');

            return $this->redirectToRoute('food_list');
        }

        return $this->render('foods/edit.html.twig', [
            'form' => $form->createView(),
            'foodName' => $food->getName()
        ]);
    }

    /**
     * Supprime un food existant.
     *
     * Exemple d'appel : DELETE /foods/42
     */
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour accéder à cette page')]
    #[Route('/{id}', name: 'food_delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em, FoodRepository $foodRepository): Response
    {
        $food = $foodRepository->find($id);

        if (!$food) {
            throw $this->createNotFoundException('Food non trouvé');
        }

        $em->remove($food);
        $em->flush();

        $this->addFlash('success', 'Food supprimé avec succès');

        return $this->redirectToRoute('food_list');
    }

    /**
     * @param int $id
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param FoodRepository $foodRepository
     * @param TagsRepository $tagsRepository
     * @return Response
     *
     * Ajouter un ou plusieurs tags
     */
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour accéder à cette page')]
    #[Route('/{id}/tags/add', name: 'food_add_tags', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function addTag(int $id, Request $request, EntityManagerInterface $em, FoodRepository $foodRepository,
    TagsRepository $tagsRepository): Response
    {
        $food = $foodRepository->find($id);

        if (!$food) {
            throw $this->createNotFoundException('Food non trouvé');
        }
        $tags = $tagsRepository->findAll();
        $foodTagsLinked = $food->getFoodTags();
        $tagsList = [];
        foreach ($foodTagsLinked as $foodTag) {
            $tagsList[] = $foodTag->getTag()->getId();
        }
        $tagsShown = array_filter($tags, fn($tag) => !in_array($tag->getId(), $tagsList));

        if ($request->isMethod('POST')) {
            $selectedTagIds = $request->request->all('tags');
            foreach ($selectedTagIds as $tagId) {
                $tagEntity = $tagsRepository->find($tagId);
                if ($tagEntity) {
                    $foodTag = new FoodTags();
                    $foodTag->setFood($food);
                    $foodTag->setTag($tagEntity);
                    $em->persist($foodTag);
                }
            }


            $em->flush();
            $this->addFlash('success', 'Tags ajoutés avec succès');

            return $this->redirectToRoute('food_list');
        }

        return $this->render('foods/add_tags.html.twig', [
            'food' => $food,
            'tags' => $tagsShown,
        ]);
    }

    /**
     * @param int $id
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param FoodRepository $foodRepository
     * @param FoodTagsRepository $foodTagsRepository
     * @return Response
     *
     * Supprimer un ou plusieurs tags
     */
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour accéder à cette page')]
    #[Route('/{id}/tags/remove', name: 'food_remove_tags', requirements: ['id' => '\d+'], methods: ['GET', 'POST'])]
    public function removeTag(int $id, Request $request, EntityManagerInterface $em, FoodRepository $foodRepository,
                              FoodTagsRepository $foodTagsRepository): Response
    {
        $food = $foodRepository->find($id);

        if (!$food) {
            throw $this->createNotFoundException('Food non trouvé');
        }
        $foodTags = $food->getFoodTags();


        if ($request->isMethod('POST')){
            $tagIds = $request->request->all('tags');
            foreach ($tagIds as $tagId) {
                $foodTag = $foodTagsRepository->findOneBy(['food' => $food, 'tag' => $tagId]);

                if ($foodTag) {
                    $em->remove($foodTag);
                }
            }

            $em->flush();
            $this->addFlash('success', 'Tags supprimés avec succès');

            return $this->redirectToRoute('food_list');
        }

        return $this->render('foods/remove_tags.html.twig', [
            'food' => $food,
            'foodTags' => $foodTags
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

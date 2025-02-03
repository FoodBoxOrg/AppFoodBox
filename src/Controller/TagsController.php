<?php

namespace App\Controller;

use App\Entity\Tags;
use App\Repository\TagsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/tags')]
final class TagsController extends AbstractController
{
    /**
     * Liste tous les Tags
     * GET /api/tags
     */
    #[Route('/', name: 'api_tags_list', methods: ['GET'])]
    public function list(TagsRepository $tagsRepository): Response
    {
        $tags = $tagsRepository->findAll();

        // On transforme chaque Tag en tableau associatif
        $data = array_map(function (Tags $tag) {
            return [
                'id'   => $tag->getId(),
                'name' => $tag->getName(),
            ];
        }, $tags);

        return $this->json($data);
    }

    /**
     * Affiche un Tag précis
     * GET /api/tags/{id}
     */
    #[Route('/{id}', name: 'api_tags_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(int $id, TagsRepository $tagsRepository): Response
    {
        $tag = $tagsRepository->find($id);

        if (!$tag) {
            return $this->json(['message' => 'Tag non trouvé'], Response::HTTP_NOT_FOUND);
        }

        return $this->json([
            'id'   => $tag->getId(),
            'name' => $tag->getName(),
        ]);
    }

    /**
     * Crée un nouveau Tag
     * POST /api/tags
     *
     * Ex. de body JSON :
     * {
     *   "name": "Italien"
     * }
     */
    #[Route('/', name: 'api_tags_create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
        TagsRepository $tagsRepository
    ): Response {
        $requestData = $request->toArray();
        $name = $requestData['name'] ?? null;

        if (!$name) {
            return $this->json(
                ['message' => 'Le champ "name" est obligatoire.'],
                Response::HTTP_BAD_REQUEST
            );
        }

        $existingTag = $tagsRepository->findOneBy(['name' => $name]);
        if ($existingTag) {
            return $this->json(
                ['message' => 'Ce tag existe déjà'],
                Response::HTTP_CONFLICT
            );
        }

        // Création du nouveau Tag
        $tag = new Tags();
        $tag->setName($name);

        $entityManager->persist($tag);
        $entityManager->flush();

        return $this->json(
            [
                'message' => 'Tag créé avec succès',
                'tag'     => [
                    'id'   => $tag->getId(),
                    'name' => $tag->getName(),
                ],
            ],
            Response::HTTP_CREATED
        );
    }
}

<?php

namespace App\Controller;

use App\Entity\Tags;
use App\Form\TagsType;
use App\Repository\TagsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/tags')]
final class TagsController extends AbstractController
{
    #[Route('/', name: 'tags_list')]
    public function list(TagsRepository $tagsRepository): Response
    {
        $tags = $tagsRepository->findAll();
        $data = [];

        foreach ($tags as $tag) {
            $data[] = [
                'id'       => $tag->getId(),
                'name'     => $tag->getName(),
            ];
        }

        return $this->render('tags/list.html.twig', [
            'tags' => $data,
        ]);
    }
    #[Route('/{id}', name: 'tags_show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(int $id, TagsRepository $tagsRepository): Response
    {
        $tag = $tagsRepository->find($id);

        return $this->render('tags/show.html.twig', [
            'tag' => $tag,
        ]);
    }
    #[IsGranted('ROLE_USER', message: 'Vous devez être connecté pour accéder à cette page')]
    #[Route('/create', name: 'tags_create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tag = new Tags();
        $form = $this->createForm(TagsType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $tag = $entityManager->getRepository(Tags::class)->findOneBy(['name' => $tag->getName()]);
            if ($tag) {
                $this->addFlash('danger', 'Ce tag existe déjà');
                return $this->redirectToRoute('tags_create');
            }
            $entityManager->persist($tag);
            $entityManager->flush();
        }

        return $this->render('tags/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}

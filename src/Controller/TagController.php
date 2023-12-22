<?php

namespace App\Controller;

use App\Entity\Tag;
use App\Form\TagType;
use App\Repository\TagRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TagController extends AbstractController
{
    #[Route('/tag/create', name: 'app_tag_create')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $tag = new Tag();

        $form = $this->createForm(TagType::class, $tag);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // $form->getData() holds the submitted values
            // but, the original `$task` variable has also been updated
            $task = $form->getData();

            // ... perform some action, such as saving the task to the database
            //save the tag
            $entityManager->persist($tag);
            $entityManager->flush();   

            return $this->redirectToRoute('app_tag_create');
        }

        return $this->render('tag/create.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/tag/success', name: 'app_tag_success')]
    public function success(): Response
    {
        return $this->render('tag/success.html.twig', [
            
        ]);
    }
    public function getTags( TagRepository $tagRepo, Request $request): Response
    {
        return $this->render('partials/tags.html.twig', [
            'tags' => $tagRepo->findAll(),
            'currentCategory' => $request->query->get('category')
        ]);
    }
}

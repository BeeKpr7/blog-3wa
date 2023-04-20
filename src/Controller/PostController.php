<?php

namespace App\Controller;

use DateTime;
use App\Entity\Post;
use App\Entity\User;
use App\Entity\Tag;
use App\Form\PostType;
use App\Repository\PostRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class PostController extends AbstractController
{
    #[IsGranted("ROLE_USER")]
    #[Route('/post/create', name: 'app_post_create')]
    public function index(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $post = new Post();
        
        $form = $this->createForm(PostType::class, $post);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post->setCreatedAt(new DateTime());
            $post->setUpdatedAt(new DateTime());
            $post->setAuthor($this->getUser());
            $post->setSlug($slugger->slug($form->get('title')->getData()));
            $imageFile = $form->get('image')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $post->setImage($newFilename);
            }
            $entityManager->persist($post);
            $entityManager->flush();   

            return $this->redirectToRoute('app_post_show', ['slug' => $post->getSlug()]);
        }

        return $this->render('post/create.html.twig', [
            'form' => $form,
        ]);
    }
    #[IsGranted("ROLE_USER")]
    #[Route('/post/edit/{slug}', name: 'app_post_edit')]
    public function update(Post $post, Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        if ($post->getAuthor() !== $this->getUser()) {
            return $this->redirectToRoute('app_post_create');
        }
        $form = $this->createForm(PostType::class, $post);
        
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $post->setCreatedAt(new DateTime());
            $post->setUpdatedAt(new DateTime());
            $post->setAuthor($this->getUser());
            $post->setSlug($slugger->slug($form->get('title')->getData()));
            $imageFile = $form->get('image')->getData();

            // this condition is needed because the 'brochure' field is not required
            // so the PDF file must be processed only when a file is uploaded
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                // this is needed to safely include the file name as part of the URL
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                // Move the file to the directory where brochures are stored
                try {
                    $imageFile->move(
                        $this->getParameter('images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // ... handle exception if something happens during file upload
                }

                // updates the 'brochureFilename' property to store the PDF file name
                // instead of its contents
                $post->setImage($newFilename);
            }
            $entityManager->persist($post);
            $entityManager->flush();   

            return $this->redirectToRoute('app_post_show', ['slug' => $post->getSlug()]);
        }

        return $this->render('post/edit.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/post/{slug}', name: 'app_post_show')]
    public function show(Post $post): Response
    {
        return $this->render('post/show.html.twig', [
            'post' => $post,
        ]);
    }

    #[Route('/post/author/{id}', name: 'app_post_author')]
    public function showAuthorPost(User $author, PostRepository $postRepo): Response
    {

        // dd($author->getPosts());
        return $this->render('home/index.html.twig', [
            'posts' => $author->getPosts(),
        ]);
    }

    #[IsGranted("ROLE_USER")]
    #[Route('/post/delete/{slug}', name: 'app_post_delete')]
    public function delete(Post $post, EntityManagerInterface $entityManager): Response
    {
        if ($post->getAuthor() !== $this->getUser()) {
            return $this->redirectToRoute('app_post_create');
        }
        $entityManager->remove($post);
        $entityManager->flush();

        return $this->redirectToRoute('app_home');
    }

    #[Route('/post/tag/{id}', name: 'app_post_tag')]
    public function showTagPost($id , PostRepository $postRepo): Response
    {
        
        $posts = $postRepo->findByTag($id);
        return $this->render('home/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    #[Route('/search', name: 'app_post_search')]
    public function search(Request $request, PostRepository $postRepo): Response
    {
        // dd($request->query->get('search'));
        $search = $request->query->get('search');
        $posts = $postRepo->findBySearch($search);
        return $this->render('home/index.html.twig', [
            'posts' => $posts,
        ]);
    }

    
}

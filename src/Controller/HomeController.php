<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/home', name: 'app_home')]
    public function index(PostRepository $postRepo): Response
    {
        return $this->render('home/index.html.twig', [
            'posts' => $postRepo->findBy([], ['createdAt' => 'DESC'], 8)
        ]);
    }

    #[Route('/api/post/{offsett}', name: 'api_post_index',)]
    public function apiIndex(PostRepository $postRepo, $offsett=0): Response
    {
        $posts = $postRepo->findBy([], ['createdAt' => 'DESC'], 6, $offsett);
        return $this->render('home/post.html.twig', [
            'posts' => $posts,
        ]);
    }
}

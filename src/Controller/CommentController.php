<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Comment;
use App\Entity\Post;
use DateTime;

class CommentController extends AbstractController
{
    #[IsGranted("ROLE_USER")]
    #[Route('/comment/create/{slug}', name: 'app_comment_create')]
    public function create(Post $post ,Request $request, EntityManagerInterface $entityManager): Response
    {
        $comment = new Comment();
        $comment->setContent($request->request->get('comment'));
        $comment->setCreatedAt(new DateTime());
        $comment->setUpdatedAt(new DateTime());
        $comment->setUser($this->getUser());
        $comment->setPost($post);

        $entityManager->persist($comment);
        $entityManager->flush();

        $this->addFlash('success','Merci pour votre commentaire');
        return $this->redirectToRoute('app_post_show', ['slug' => $post->getSlug()]);
    }
}

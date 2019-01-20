<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\CommentFormType;
use App\Repository\ArticleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class ArticleController extends AbstractController
{
    /**
     * @Route("/", name="app_homepage")
     */
    public function homepage(ArticleRepository $repository, Request $request)
    {
        $articles = $repository->findAllPublishedOrderedByNewest();

        return $this->render(
            'article/homepage.html.twig',
            [
                'articles' => $articles,
            ]
        );
    }

    /**
     * @Route("/news/{slug}", name="article_show")
     */
    public function show(Article $article)
    {
        $form = $this->createForm(CommentFormType::class);

        return $this->render(
            'article/show.html.twig',
            [
                'article' => $article,
                'commentForm' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/news/{slug}/heart", name="article_toggle_heart", methods={"POST"})
     */
    public function toggleArticleHeart(Article $article, LoggerInterface $logger, EntityManagerInterface $em)
    {
        $article->incrementHeartCount();
        $em->flush();
        $logger->info('Article is being hearted!');

        return new JsonResponse(['hearts' => $article->getHeartCount()]);
    }

    /**
     * @Route("/news/{slug}/comment", name="article_comment", methods={"POST"})
     * @IsGranted("ROLE_USER")
     */
    public function comment(Article $article, Request $request, EntityManagerInterface $em)
    {
        /** @var Comment $commentContent */
        $commentContent = $request->request->get('comment_form') ?? null;
        $isFilled = $commentContent && $commentContent['content'];

        if (null === $isFilled
            || '' === $commentContent['content']) {
            return $this->redirectToRoute(
                'article_show',
                array('slug' => $article->getSlug())
            );
        };
        $user = $this->getUser();

        $comment = new Comment();
        $comment->setContent(
            $commentContent['content']
        );

        $comment->setAuthor($user);
        $comment->setCreatedAt(date_create('now'));
        $comment->setArticle($article);
        $comment->setIsDeleted(false);

        $article->addComment($comment);
        $em->persist($comment);
        $em->flush();

        $this->addFlash('success', 'Comment Added! Inaccuracies squashed!');

        return $this->redirectToRoute(
            'article_show',
            array('slug' => $article->getSlug())
        );
    }
}
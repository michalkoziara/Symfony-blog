<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class CommentAdminController extends AbstractController
{
    /**
     * @Route("/admin/comment", name="comment_admin")
     * @IsGranted("ROLE_ADMIN_COMMENT")
     */
    public function list(CommentRepository $repository, Request $request, PaginatorInterface $paginator)
    {
        $q = $request->query->get('q');

        $queryBuilder = $repository->getWithSearchQueryBuilder($q);

        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render(
            'comment_admin/index.html.twig',
            [
                'pagination' => $pagination,
            ]
        );
    }

    /**
     * @Route("/admin/comment/{id}/delete", name="admin_comment_delete")
     * @IsGranted("MANAGE", subject="comment")
     */
    public function delete(Comment $comment, EntityManagerInterface $em)
    {
        $em->remove($comment);
        $em->flush();
        $this->addFlash('success', 'Comment Deleted! Inaccuracies squashed!');

        return $this->redirectToRoute(
            'comment_admin'
        );
    }

    /**
     * @Route("/admin/comment/{id}/block", name="admin_comment_block")
     * @IsGranted("MANAGE", subject="comment")
     */
    public function block(Comment $comment, EntityManagerInterface $em)
    {
        $comment->setIsDeleted(true);
        $em->persist($comment);
        $em->flush();
        $this->addFlash('success', 'Comment Blocked! Inaccuracies squashed!');

        return $this->redirectToRoute(
            'comment_admin'
        );
    }
}

<?php

namespace App\Controller;


use App\Entity\User;
use App\Form\ArticleFormType;
use App\Repository\ArticleRepository;
use App\Service\SlackClient;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use App\Entity\Article;

class ArticleAdminController extends AbstractController
{
    /**
     * @Route("/admin/article/new", name="admin_article_new")
     * @IsGranted("ROLE_ADMIN_ARTICLE")
     */
    public function new(EntityManagerInterface $em, Request $request, SlackClient $slack)
    {
        $form = $this->createForm(ArticleFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            /** @var UploadedFile $file */
            $file = $form['logo']->getData();

            if ('.png' !== $file->getClientOriginalExtension()) {
                $this->addFlash('error', 'Błędne rozszerzenie pliku!');

                return $this->redirectToRoute('app_homepage');
            }

            $file->move('images/', $file->getClientOriginalName());

            /** @var Article $article */
            $article = $form->getData();
            $article->setImageFilename($file->getClientOriginalName());

            $em->persist($article);
            $em->flush();

            /** @var User $user */
            $user = $this->getUser();

            $slack->sendMessage($user, 'Dodałem nowy artykuł! Sprawdźcie :)');
            $this->addFlash('success', 'Artykuł dodany!');

            return $this->redirectToRoute('app_homepage');
        }

        return $this->render(
            'article_admin/new.html.twig',
            [
                'articleForm' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/admin/article/{id}/edit", name="admin_article_edit")
     * @IsGranted("MANAGE", subject="article")
     */
    public function edit(Article $article, Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(
            ArticleFormType::class,
            $article,
            [
                'include_published_at' => true,
            ]
        );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Article $article */
            $article = $form->getData();
            $em->persist($article);
            $em->flush();
            $this->addFlash('success', 'Artykuł zaktualizowany!');

            return $this->redirectToRoute(
                'admin_article_edit',
                [
                    'id' => $article->getId(),
                ]
            );
        }

        return $this->render(
            'article_admin/edit.html.twig',
            [
                'articleForm' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/admin/article/{id}/delete", name="admin_article_delete")
     * @IsGranted("MANAGE", subject="article")
     */
    public function delete(Article $article, EntityManagerInterface $em)
    {
        $fileSystem = new Filesystem();

        try {
            $fileSystem->remove($article->getImagePath());
        } catch (IOExceptionInterface $exception) {
            echo "An error occurred while removing logo!";
        }

        foreach ($article->getComments() as $comment) {
            $em->remove($comment);
        }
        $em->remove($article);
        $em->flush();
        $this->addFlash('success', 'Artykuł usunięty!');

        return $this->redirectToRoute(
            'admin_article_list'
        );
    }

    /**
     * @Route("/admin/article", name="admin_article_list")
     * @IsGranted("ROLE_ADMIN_ARTICLE")
     */
    public function list(ArticleRepository $articleRepo)
    {
        $articles = $articleRepo->findAll();

        return $this->render(
            'article_admin/list.html.twig',
            [
                'articles' => $articles,
            ]
        );
    }
}
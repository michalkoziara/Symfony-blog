<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Comment;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class CommentFixture extends BaseFixture implements DependentFixtureInterface
{
    protected function loadData(ObjectManager $manager)
    {
        $this->createMany(
            100,
            'comments',
            function ($count) {
                $comment = new Comment();
                $comment->setContent(
                    $this->faker->boolean ? $this->faker->paragraph : $this->faker->sentences(2, true)
                );

                $comment->setAuthor($this->getRandomReference('main_users'));
                $comment->setCreatedAt($this->faker->dateTimeBetween('-1 months', '-1 seconds'));
                $comment->setArticle($this->getRandomReference('main_articles'));
                $comment->setIsDeleted($this->faker->boolean(20));

                return $comment;
            }
        );


        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            ArticleFixtures::class,
            UserFixture::class,
        ];
    }
}

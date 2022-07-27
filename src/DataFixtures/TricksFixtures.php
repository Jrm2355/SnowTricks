<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Trick;
use App\Entity\Media;
use App\Entity\Category;
use App\Entity\Comment;

class TricksFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $trick = new Trick();
        $category = new Category();
        $comment = new Comment();
        $media = new Media();

        $trick->setName('');
        $trick->setDescription('');
        $trick->setCategory($category);
        $trick->addComment($comment);
        $trick->addMedium($media);


        $manager->persist($trick);

        $manager->flush();
    }
}

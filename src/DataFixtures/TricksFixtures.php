<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Trick;
use App\Entity\Media;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\User;
use Symfony\Component\Validator\Constraints\Cascade;

class TricksFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        //$userName = ["Jyon", "Shaunzy", "Toto"];
        $userEmail = ["jeremyon.jy@gmail.com", "shaun.white@gmail.com", "tony.hawk@gmail.com"];
        $userPassword = ["jyon01", "swhite01", "thawk01"];
        $userFirstName = ["Jeremy", "Shaun", "Tony"];
        $userLastName = ["Yon", "White", 'Hawk'];
        $userPicture = ["img/icons/team-1.png", "img/icons/team-2.png", "img/icons/team-3.png"];
        
        $categoryName = ["grab", "rotation", "flip", "slide"];

        $tricksName = ["mute","sad","indy","stalefish","tail grab","180","360", "front flip", "back flip","nose slide"];
        $tricksDescription = ["saisie de la carre frontside de la planche entre les deux pieds avec la main avant",
        "saisie de la carre backside de la planche, entre les deux pieds, avec la main avant",
        "saisie de la carre frontside de la planche, entre les deux pieds, avec la main arrière",
        "saisie de la carre backside de la planche entre les deux pieds avec la main arrière",
        "saisie de la partie arrière de la planche, avec la main arrière",
        "un 180 désigne un demi-tour, soit 180 degrés d'angle",
        "360, trois six pour un tour complet",
        "front flips, rotations en avant",
        "les back flips, rotations en arrière",
        "nose slide, c'est-à-dire l'avant de la planche sur la barre" ];
        $tricksFkUser = array(0=>0,1=>0,2=>0,3=>1,4=>1,5=>1,6=>2,7=>2,8=>2,9=>2);
        $tricksFkCategory = ["0","0","0","0","0","1","1","2","2","3"];

        $mediaSource = [];
        //$mediaType = all pictures for now
        //$mediaFkTricks = index 1 par tricks

        //$comment = Ajout avec le crud ? trick et user peuvent avoir 0 comment

        //UserSet
        for ($i = 0; $i < count($userEmail); $i++) {
            $user = new User();

            $user->setEmail($userEmail[$i]);
            $user->setPassword($userPassword[$i]);
            $user->setFirstname($userFirstName[$i]);
            $user->setLastname($userLastName[$i]);
            $user->setPicture($userPicture[$i]);

            $userArray = array($i=>$user); // for fk trick
            //array_push($userArray, $user);

            $manager->persist($user);            
            $manager->flush();
        }

        //CategorySet
        for ($i = 0; $i < count($categoryName); $i++) {
            $category = new Category();

            $category->setName($categoryName[$i]);

            $categoryArray = array();  //for fk trick
            array_push($categoryArray, $category);

            $manager->persist($category);
            $manager->flush();
        }

        //TrickSet
        for ($i = 0; $i < count($tricksName); $i++) {
            $trick = new Trick();

            $trick->setName($tricksName[$i]);
            $trick->setDescription($tricksDescription[$i]);
            $trick->setUser($user);
            $trick->setCategory($category);
            //$trick->setUser($userArray[$tricksFkUser[$i]]);
            //$trick->setCategory($categoryArray[$tricksFkCategory[$i]]);

            $manager->persist($trick);
            $manager->flush();
        }
        //$manager->flush();
    }
}

<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Trick;
use App\Entity\Media;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Constraints\Cascade;
use DateTime;

class TricksFixtures extends Fixture
{
    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
    //déclaration des données
    $users = [
        [
            "firstname"=>"Jeremy",
            "lastname"=>"Yon",
            "username"=>"Jyon",
            "email"=>"jeremyon.jy@gmail.com",
            "password"=>"adminpassword",
            "picture"=>"img/icons/team-1.png",
            "roles"=>['ROLE_ADMIN', 'ROLE_USER'],
            "category"=>[
                [
                    "name"=>"grab",
                    "tricks"=>[
                        [
                            "name"=>"mute",
                            "description"=>"saisie de la carre frontside de la planche entre les deux pieds avec la main avant",
                            "media"=>[
                                [
                                    "source"=>"img/media/mute.png",
                                    "type"=>"picture",
                                ],
                            ],
                        ],
                        [
                            "name"=>"sad",
                            "description"=>"saisie de la carre backside de la planche, entre les deux pieds, avec la main avant",
                            "media"=>[
                                [
                                    "source"=>"img/media/sad.png",
                                    "type"=>"picture",
                                ],
                            ],
                        ],
                        [
                            "name"=>"indy",
                            "description"=>"saisie de la carre frontside de la planche, entre les deux pieds, avec la main arrière",
                            "media"=>[
                                [
                                    "source"=>"img/media/indy.png",
                                    "type"=>"picture",
                                ],
                            ],
                        ],
                        [
                            "name"=>"stalefish",
                            "description"=>"saisie de la carre backside de la planche entre les deux pieds avec la main arrière",
                            "media"=>[
                                [
                                    "source"=>"img/media/stalefish.png",
                                    "type"=>"picture",
                                ],
                            ],
                        ],
                    ],                
                ],
            ],
        ],
        [
            "firstname"=>"Shaun",
            "lastname"=>"White",
            "username"=>"SWhite",
            "email"=>"shaun.white@gmail.com",
            "password"=>"userpassword",
            "picture"=>"img/icons/team-2.png",
            "roles"=>['ROLE_USER'],
            "category"=>[
                [
                    "name"=>"grab",
                    "tricks"=>[
                        [
                            "name"=>"tail grab",
                            "description"=>"saisie de la partie arrière de la planche, avec la main arrière",
                            "media"=>[
                                [
                                    "source"=>"img/media/tailgrabe.png",
                                    "type"=>"picture",
                                ],
                            ],
                        ],  
                    ],                
                ],
                [
                    "name"=>"rotation",
                    "tricks"=>[
                        [
                            "name"=>"180",
                            "description"=>"un 180 désigne un demi-tour, soit 180 degrés d'angle",
                            "media"=>[
                                [
                                    "source"=>"img/media/180.png",
                                    "type"=>"picture",
                                ],
                            ],
                        ],
                        [
                            "name"=>"360",
                            "description"=>"360, trois six pour un tour complet",
                            "media"=>[
                                [
                                    "source"=>"img/media/360.png",
                                    "type"=>"picture",
                                ],
                            ],
                        ]
                    ],
                ],
            ]
        ],
        [
            "firstname"=>"Tony",
            "lastname"=>"Hawk",
            "username"=>"THawk",
            "email"=>"tony.hawk@gmail.com",
            "password"=>"userpassword",
            "picture"=>"img/icons/team-3.png",
            "roles"=>['ROLE_USER'],
            "category"=>[
                [
                    "name"=>"flip",
                    "tricks"=>[
                        [
                            "name"=>"front flip",
                            "description"=>"front flips, rotations en avant",
                            "media"=>[
                                [
                                    "source"=>"img/media/frontflip.png",
                                    "type"=>"picture",
                                ],
                            ],
                        ],
                        [
                            "name"=>"back flip",
                            "description"=>"les back flips, rotations en arrière",
                            "media"=>[
                                [
                                    "source"=>"img/media/backflip.png",
                                    "type"=>"picture",
                                ],
                            ],
                        ],
                    ],
                ],
                [
                    "name"=>"slide",
                    "tricks"=>[
                        [
                            "name"=>"nose slide",
                            "description"=>"nose slide, c'est-à-dire l'avant de la planche sur la barre",
                            "media"=>[
                                [
                                    "source"=>"img/media/noseslide.png",
                                    "type"=>"picture",
                                ],
                            ],
                        ]
                    ]                
                ],
            ],
        ],
    ];

        //parcours et enregistrement des données
        foreach ($users as $u) {
            $user = new User();
            $user->setEmail($u["email"]);
            $user->setPassword(
                $this->passwordHasher->hashPassword(
                    $user,
                    $u["password"]));  
            $user->setFirstname($u["firstname"]);
            $user->setLastname($u["lastname"]);
            $user->setUsername($u["username"]);
            $user->setPicture($u["picture"]);
            $user->setRoles($u["roles"]);
            $manager->persist($user);

            foreach ($u["category"] as $c) {
                $category = new Category();
                $category->setName($c["name"]);
                $manager->persist($category);

                foreach ($c["tricks"] as $t) {
                    $trick = new Trick();
                    $trick->setName($t["name"]);
                    $trick->setDescription($t["description"]);
                    $trick->setUser($user);
                    $trick->setCategory($category);
                    $manager->persist($trick);

                    foreach ($t["media"] as $m) {
                        $media = new Media();
                        $media->setSource($m["source"]);
                        $media->setType($m["type"]);
                        $media->setTrick($trick);
                        $manager->persist($media);
                    }

                    $comment = new Comment();
                    $comment->setContent("Mon premier commentaire");
                    $comment->setUser($user);
                    $comment->setTrick($trick);
                    $date = new DateTime();
                    $comment->setDateCreation($date);
                    $manager->persist($comment);
                }
            }
        }
        $manager->flush();
    }
}

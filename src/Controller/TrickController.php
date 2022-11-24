<?php

namespace App\Controller;

use App\Entity\Trick;
use App\Entity\Comment;
use App\Entity\Media;
use App\Form\TrickType;
use App\Form\CommentType;
use App\Form\MediaType;
use App\Repository\TrickRepository;
use App\Repository\CommentRepository;
use App\Repository\MediaRepository;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/')]
class TrickController extends AbstractController
{
    #[Route('/', name: 'app_trick_index', methods: ['GET'])]
    public function index(TrickRepository $trickRepository): Response
    {
        $tricks = $trickRepository->findAll();
        
        return $this->render('trick/index.html.twig', [
            'tricks' => $tricks,
        ]);
    }

    #[Route('/new', name: 'app_trick_new', methods: ['GET', 'POST'])]
    public function new(Request $request, TrickRepository $trickRepository, MediaRepository $mediaRepository): Response
    {
        $trick = new Trick();
        $trick_form = $this->createForm(TrickType::class, $trick);
        $trick_form->handleRequest($request);

        if ($trick_form->isSubmitted() && $trick_form->isValid()) {
            $mediaPhoto = $trick_form->get('media')->getData();
            $trick->setUser($this->getUser());
            $trickRepository->add($trick, true);
            foreach ($mediaPhoto as $medium) {
                // On génère un nouveau nom de fichier 
                $fichier = '/img/media/'.md5(uniqid()).'.'.'jpg';
                // On copie le fichier dans le dossier uploads 
                $medium->move( $this->getParameter('media_directory'), $fichier ); 
                // On crée l'image dans la base de données 
                $newMedia = new Media(); 
                $newMedia->setSource($fichier); 
                $newMedia->setType('picture');
                $newMedia->setTrick($trick);
                $newMedia->setMain(true);
                $mediaRepository->add($newMedia, true);
            }
            $mediaVideoPath = $trick_form->get('mediaVideo')->getData();
            if($mediaVideoPath !== null) {
                $newMediaVideo = new Media();
                $newMediaVideo->setSource($mediaVideoPath); 
                $newMediaVideo->setType('video');
                $newMediaVideo->setTrick($trick);
                $newMediaVideo->setMain(false);
                $mediaRepository->add($newMediaVideo, true); 
            }

            return $this->redirectToRoute('app_trick_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('trick/new.html.twig', [
            'trick' => $trick,
            'trick_form' => $trick_form,
        ]);
    }

    #[Route('/{id}', name: 'app_trick_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Trick $trick, CommentRepository $commentRepository): Response
    {
        $comment = new Comment();
        $comment_form = $this->createForm(CommentType::class, $comment);
        $comment_form->handleRequest($request);

        if ($comment_form->isSubmitted() && $comment_form->isValid()) {
            $comment->setTrick($trick);
            $comment->setDateCreation(new \DateTime());
            $comment->setUser($this->getUser());
            $commentRepository->add($comment, true);
        }

        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'comment_form' => $comment_form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_trick_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Trick $trick, TrickRepository $trickRepository, MediaRepository $mediaRepository): Response
    {
        $trick_form = $this->createForm(TrickType::class, $trick);
        $trick_form->handleRequest($request);

        if ($trick_form->isSubmitted() && $trick_form->isValid()) {
            $trickRepository->add($trick, true);
            return $this->redirectToRoute('app_trick_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('trick/edit.html.twig', [
            'trick' => $trick,
            'trick_form' => $trick_form,
        ]);
    }

    #[Route('/{id}', name: 'app_trick_delete')]
    public function delete(Request $request, Trick $trick, TrickRepository $trickRepository): Response
    {
        $trickRepository->remove($trick, true);
        $this->addFlash('success', 'La tâche a bien été supprimée.');
        return $this->redirectToRoute('app_trick_index');
    }
}

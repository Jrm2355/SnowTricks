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
    public function index(TrickRepository $trickRepository, Request $request): Response
    {
        $page = $request->get('page', 1);
        // $tricks = $trickRepository->findAll();
        $tricks = $trickRepository->findBy([], [], 8 + (4 * ($page - 1)),0);
        return $this->render('trick/index.html.twig', [
            'tricks' => $tricks,
            'page' => $page,
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
            $mainControle = 0;
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
                if($mainControle == 0){
                    $newMedia->setMain(true);
                    $mainControle = 1;
                } else{
                    $newMedia->setMain(false);
                }
                $mediaRepository->add($newMedia, true);
            }
            $mediaVideo = $trick_form->get('mediaVideo')->getData();
            if($mediaVideo !== null) {
                $mediaVideoPath = explode("\n", $mediaVideo);
                foreach ($mediaVideoPath as $video){
                    $newMediaVideo = new Media();
                    $newMediaVideo->setSource($video); 
                    $newMediaVideo->setType('video');
                    $newMediaVideo->setTrick($trick);
                    $newMediaVideo->setMain(false);
                    $mediaRepository->add($newMediaVideo, true); 
                }
            }

            $this->addFlash('success', 'Le nouveau trick a été ajouté !');
            return $this->redirectToRoute('app_trick_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('trick/new.html.twig', [
            'trick' => $trick,
            'trick_form' => $trick_form,
        ]);
    }

    #[Route('/{id}', name: 'app_trick_show', methods: ['GET', 'POST'])]
    public function show(Request $request, Trick $trick, CommentRepository $commentRepository, MediaRepository $mediaRepository): Response
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

        $mediaMain = $mediaRepository->findBy(array('trick' => $trick->getId(), 'main' => true));
        if ($mediaMain == []){
            $mediaMain = [New Media];
        }
        $mediaAutre = $mediaRepository->findBy(['trick' => $trick->getId(), 'main' => false]);
        return $this->render('trick/show.html.twig', [
            'trick' => $trick,
            'comment_form' => $comment_form->createView(),
            'mediaMain' => $mediaMain[0],
            'media' => $mediaAutre
        ]);
    }

    #[Route('/{id}/edit', name: 'app_trick_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Trick $trick, TrickRepository $trickRepository, MediaRepository $mediaRepository): Response
    {
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
                $newMedia->setMain(false);
                $mediaRepository->add($newMedia, true);
            }
            $mediaVideo = $trick_form->get('mediaVideo')->getData();
            if ($mediaVideo !== null) {
                $mediaVideoPath = explode("\n", $mediaVideo);
                foreach ($mediaVideoPath as $video) {
                    $newMediaVideo = new Media();
                    $newMediaVideo->setSource($video);
                    $newMediaVideo->setType('video');
                    $newMediaVideo->setTrick($trick);
                    $newMediaVideo->setMain(false);
                    $mediaRepository->add($newMediaVideo, true);
                }
            }

            return $this->redirectToRoute('app_trick_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('trick/edit.html.twig', [
            'trick' => $trick,
            'trick_form' => $trick_form,
            'media' => $trick->getMedia(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_trick_delete')]
    public function delete(Request $request, Trick $trick, TrickRepository $trickRepository): Response
    {
        $trickRepository->remove($trick, true);
        $this->addFlash('success', 'La tâche a bien été supprimée.');
        return $this->redirectToRoute('app_trick_index');
    }

    #[Route('/{id}/main', name: 'app_media_main')]
    public function becomeMain(Request $resquest, Media $media, MediaRepository $mediaRepository): Response
    {
        $media->setMain(true);
        $trickId = $media->getTrick();
        $mediaRepository->add($media, true);

        $oldMediaMain = $mediaRepository->findOneBy(array('trick' => $trickId, 'main' => true));
        // dd($oldMediaMain);
        $oldMediaMain->setMain(false);
        $mediaRepository->add($oldMediaMain, true);
        return $this->redirectToRoute('app_trick_index');
    }
}

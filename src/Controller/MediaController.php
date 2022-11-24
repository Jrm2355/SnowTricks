<?php

namespace App\Controller;

use App\Entity\Media;
use App\Repository\MediaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MediaController extends AbstractController
{
    #[Route('media/{id}/delete', name: 'app_media_delete')]
    public function deleteMedia(MediaRepository $mediaRepository, Media $media): Response
    {
        $mediaRepository->remove($media, true);
        $this->addFlash('success', 'Le médium a bien été supprimé.');
        return $this->redirectToRoute('app_trick_index');
    }
}

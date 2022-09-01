<?php

namespace App\Controller;

use App\Repository\InviteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InformationsController extends AbstractController
{
    #[Route('/informations/{slug}', name: 'app_informations')]
    public function index($slug, InviteRepository $invitRipo): Response
    {
        $invite = $invitRipo->findOneBy(['slug'=>$slug]);
        return $this->render('informations/index.html.twig', [
            'invite'=>$invite,
            'qrcode'=>$invite->getSlug().'.png'
        ]);
    }
}

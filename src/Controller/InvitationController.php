<?php

namespace App\Controller;

use App\Repository\InviteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class InvitationController extends AbstractController
{
    #[Route('/invitation/{slug}', name: 'app_invitation')]
    public function index($slug, InviteRepository $inviteRepository): Response
    {

        return $this->render('invitation/index.html.twig', [
           'invit'=>$inviteRepository->findOneBy(['slug'=>$slug])
        ]);
    }
}

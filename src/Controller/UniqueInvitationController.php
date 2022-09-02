<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UniqueInvitationController extends AbstractController
{
    #[Route('/unique/invitation', name: 'app_unique_invitation')]
    public function index(): Response
    {
        return $this->render('unique_invitation/index.html.twig', [
            'controller_name' => 'UniqueInvitationController',
        ]);
    }
}

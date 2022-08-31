<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminGalerieController extends AbstractController
{
    #[Route('/admin/galerie', name: 'app_admin_galerie')]
    public function index(): Response
    {
        return $this->render('admin_galerie/index.html.twig', [
            'controller_name' => 'AdminGalerieController',
        ]);
    }
}

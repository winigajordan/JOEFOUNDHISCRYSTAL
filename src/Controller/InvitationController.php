<?php

namespace App\Controller;

use App\Repository\InviteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/invitation')]
class InvitationController extends AbstractController
{
    #[Route('/{slug}', name: 'invitation')]
    public function index($slug, InviteRepository $inviteRepository): Response
    {
        return $this->render('invitation/index.html.twig', [
           'invit'=>$inviteRepository->findOneBy(['slug'=>$slug])
        ]);
    }

    #[Route('/update/validation', name: 'invitation_validation')]
    public function update(Request $request, EntityManagerInterface $em, InviteRepository $invitRipo){
        $data = $request->request;
        $invite = $invitRipo->findOneBy(['slug'=>$data->get('slug')]);
        if (!$data->get('validation')){
            $invite->setPlace(null);
            $invite->setType("VIRTUEL");
        }
        $invite->setValide(true);
        $em->persist($invite);
        $em->flush();

        return $this->redirectToRoute('admin');
    }
}

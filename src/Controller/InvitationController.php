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
        $invit = $inviteRepository->findOneBy(['slug'=>$slug, 'type'=>'PHYSIQUE']);
        if ($invit==null){
            return $this->redirectToRoute('app_home');
        }
        if ($invit->isValide()){
            return $this->redirectToRoute('app_home');
        }
        return $this->render('invitation/index.html.twig', [
           'invit'=>$invit
        ]);
    }

    #[Route('/update/validation', name: 'invitation_validation')]
    public function update(Request $request, EntityManagerInterface $em, InviteRepository $invitRipo){
        $data = $request->request;
        $invite = $invitRipo->findOneBy(['slug'=>$data->get('slug')]);
        $invite->setValide(true);
        if ($data->get('validation')=='no'){
            $invite->setPlace(null);
            $invite->setType("VIRTUEL");
            $em->persist($invite);
            $em->flush();
            return $this->redirectToRoute('app_home');
        } else {
            return $this->redirectToRoute('app_informations', ['slug'=>$data->get('slug')]);
        }
        //dd($invite);




    }
}

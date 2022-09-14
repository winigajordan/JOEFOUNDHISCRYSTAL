<?php

namespace App\Controller;

use App\Repository\InvitationsEnvoyeRepository;
use App\Repository\InviteRepository;
use App\Service\MessageSender\WhatsAppApi;
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
            return $this->redirectToRoute('app_informations', ['slug'=>$slug]);
        }
        return $this->render('invitation/index.html.twig', [
           'invit'=>$invit
        ]);
    }

    #[Route('/update/validation', name: 'invitation_validation')]
    public function update(Request $request,
                           EntityManagerInterface $em,
                           InviteRepository $invitRipo,
                           InvitationsEnvoyeRepository $sentRipo,
                            WhatsAppApi $api
    ){
        $data = $request->request;
        $invite = $invitRipo->findOneBy(['slug'=>$data->get('slug')]);
        $invite->setValide(true);
        //dd($data);
        if ($data->get('validation')=='no'){
            $invite->setPlace(null);
            $invite->setType("VIRTUEL");
            $invite->setValide(false);
            $sent = $sentRipo->findOneBy(['invite'=>$invite]);
            $em->persist($invite);
            $em->remove($sent);
            if($invite->getHerPlace()){
                $em->remove($invite->getHerPlace());
                
            }
            $em->flush();
            return $this->redirectToRoute('app_home');
        }
        $em->flush();
        $api->img($invite->getTelephone(),
            link: 'https://www.qrcode-monkey.com/img/qrcode-logo.png',
            message: $this->messageText('Ce code QR vous redirigera vers la page de vos informations')
        );
        return $this->redirectToRoute('app_informations', ['slug'=>$data->get('slug')]);

        
    }

    public function messageText($text){
        $msg = str_replace(' ', '%20', $text);
        $msg = str_replace('/', '%2F',$msg);
        return $msg;
    }
}
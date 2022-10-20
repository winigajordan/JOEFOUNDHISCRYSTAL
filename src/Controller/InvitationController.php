<?php

namespace App\Controller;

use App\Entity\Invite;
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
            $invit = new Invite();
            $invit->setNom($invite->getNom());
            $invit->setPrenom($invite->getPrenom());
            $invit->setTelephone($invite->getTelephone());
            $invit->setAdresse($invite->getAdresse());
            $invit->setPhoto($invite->getPhoto());
            $invit->setSituation($invite->getSituation());
            $invit->setType("VIRTUEL");
            $invit->setPlace(null);
            $invit->setEmail("");
            $invit->setValide(true);
            $invit->setCivilite($invite->getCivilite());
            $invit->setHerName($invite->getHerName());
            $invit->setSlug($invite->getSlug());
           
            $invite->setType("VIRTUEL");
            $invite->setValide(false);
            $sent = $sentRipo->findOneBy(['invite'=>$invite]);
            $em->remove($invite);
            $em->persist($invit);

            
            if($invite->getHerPlace()){
                $em->remove($invite->getHerPlace());
            }
            $em->flush();
            return $this->redirectToRoute('app_home');
        }
        $em->flush();
        $api->img($invite->getTelephone(),
            link: 'https://joefoundhiscrystal2022.com/Files/qrcode/'.$invite->getSlug().'.png',
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
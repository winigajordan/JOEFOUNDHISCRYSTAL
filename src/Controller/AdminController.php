<?php

namespace App\Controller;

use App\Entity\HerPlace;
use App\Entity\InvitationsEnvoye;
use App\Entity\Invite;
use App\Entity\Table;
use App\Entity\User;
use App\Repository\DemandeRepository;
use App\Repository\HerPlaceRepository;
use App\Repository\InvitationsEnvoyeRepository;
use App\Repository\InviteRepository;
use App\Repository\ReunionRepository;
use App\Repository\SalleRepository;
use App\Repository\TableRepository;
use App\Service\MessageSender\WhatsAppApi;
use Doctrine\ORM\EntityManagerInterface;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin'), IsGranted("ROLE_ADMIN")]
class AdminController extends AbstractController
{
    private SalleRepository $salleRipo;
    private TableRepository $tableRipo;
    private EntityManagerInterface $em;
    private InviteRepository $inviteRipo;
    private DemandeRepository $demandeRipo;
    private ReunionRepository $reunionRipo;
    private InvitationsEnvoyeRepository $invitSendRipo;
    private $encoder;
    private HerPlaceRepository $hpRipo;

    public function __construct(
        SalleRepository $salleRipo,
        EntityManagerInterface $em,
        TableRepository $tableRipo,
        InviteRepository $inviteRipo,
        DemandeRepository $demandeRipo,
        ReunionRepository $reunionRipo,
        InvitationsEnvoyeRepository $invitSendRipo,
        UserPasswordHasherInterface $encoder,
        HerPlaceRepository $hpRipo,
    )
    {
        $this->em = $em;
        $this->salleRipo = $salleRipo;
        $this->tableRipo = $tableRipo;
        $this->inviteRipo = $inviteRipo;
        $this->reunionRipo = $reunionRipo;
        $this->reunion = $reunionRipo->find(1);
        $this->salle = $salleRipo->find(1);
        $this->demandes = $demandeRipo->findBy(['etat'=>false]);
        $this->demandeRipo = $demandeRipo;
        $this->encoder = $encoder;
        $this->hpRipo = $hpRipo;

    }


    #[Route('', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'salle'=>$this->salle->getNom(),
            'reunion'=> $this->reunion,
            'tables'=>$this->tableRipo->findAll(),
            'invites'=>$this->inviteRipo->findAll(),
            'demandes'=>$this->demandes
        ]);
    }

    #[Route('/update/name', name: 'update_name')]
    public function updateName(Request $request): Response
    {
        $salle =$this->salleRipo->find(1);
        $salle->setNom($request->request->get('nom'));
        $this->em->persist($salle);
        $this->em->flush();
       return $this->redirectToRoute('admin');
    }

    #[Route('/reunion/update', name: 'update_link', methods: 'post')]
    public function updateLink(Request $request){
        $this->reunion->setUrl($request->request->get('link'));
        $this->reunion->setPassword($request->request->get('password'));
        $this->em->persist($this->reunion);
        $this->em->flush();
        return $this->redirectToRoute('admin');
    }

    #[Route('/table/add', name:'add_table')]
    public function addTable(Request $request){
        $table = new Table();
        $table -> setNom($request->request->get('nom'));
        $table -> setSlug(uniqid('table-'));
        $table -> setSalle($this->salle);
        $this->em->persist($table);
        $this->em->flush();
        return $this->redirectToRoute('admin');
    }

    #[Route('/table/{slug}', name:'dtl_table')]
    public function detailsTable($slug)
    {
        return $this->render('admin/index.html.twig', [
            'salle'=>$this->salle->getNom(),
            'reunion'=> $this->reunion,
            'tables'=>$this->tableRipo->findAll(),
            'demandes'=>$this->demandes,
            'table_selected'=>$this->tableRipo->findOneBy(['slug'=>$slug]),
            'invites'=>$this->inviteRipo->findAll()
        ]);
    }

    #[Route('/table/data/update', name:'update_table')]
    public function updateTable(Request $request){
        $table = $this->tableRipo->find($request->request->get('id'));
        $table -> setNom($request->request->get('nom'));
        $this->em->persist($table);
        $this->em->flush();
        return $this->redirectToRoute('admin');
    }


    #[Route('/invite/add', name:'add_invite')]
    public function addInvite(Request $request){
        $data = $request->request;
        $invite = new Invite();
        $invite->setSlug(uniqid('invit-'));
        $invite->setNom($data->get('nom'));
        $invite->setPrenom($data->get('prenom'));
        $invite->setEmail('');
        $invite->setAdresse($data->get('adresse'));
        $invite->setTelephone(str_replace(' ', '', $data->get('telephone')));
        $invite->setSituation($data->get('situation'));
        $invite->setValide(false);

        if ($data->get('hername')!= null){
            $invite->setHerName($data->get('hername'));
        }

        //ajout du code qr
        $this->generateQrCode($invite->getSlug());

        //ajout d'image
        $img=$request->files->get("image");
        $imageName=uniqid().'.'.$img->guessExtension();
        $img->move($this->getParameter("profile"),$imageName);
        $invite->setPhoto($imageName);

        //traitement du type
        if ($data->get('type')=="VIRTUEL"){
            $invite->setType($data->get('type'));
        } else {
            $tab = $this->tableRipo->findOneBy(['slug'=>$data->get('type')]);
            $invite->setType("PHYSIQUE");
            $invite->setPlace($tab);
            //verifie si il ya une conjointe
            if (!empty($data->get('hername'))){
                if ((count($tab->getHerPlaces()) + count($tab->getInvites()) )>8)
                {
                    $this->addFlash('table_waring', 'Cette table ne peut pas accueillir un couple');
                    return $this->redirectToRoute('admin');
                } else
                {
                    $hp = (new HerPlace())
                        ->setInvite($invite)
                        ->setPlace($tab);
                    $this->em->persist($hp);
                }
            }
        }
        $this->em->persist($invite);
        $this->em->flush();
        return $this->redirectToRoute('admin');
    }

    #[Route('/invite/{slug}', name:'dtl_invite')]
    public function dtlInvite($slug){
        return $this->render('admin/index.html.twig', [
            'salle'=>$this->salle->getNom(),
            'reunion'=> $this->reunion,
            'tables'=>$this->tableRipo->findAll(),
            'invites'=>$this->inviteRipo->findAll(),
            'demandes'=>$this->demandes,
            'invite_selected'=>$this->inviteRipo->findOneBy(['slug'=>$slug]),
        ]);
    }

    #[Route('/invite/data/update', name:'update_invite')]
    public function updateInvite(Request $request){
        $data = $request->request;
        $invite = $this->inviteRipo->findOneBy(['slug'=>$data->get('slug')]);
        //dd($data);
        $invite->setNom($data->get('nom'));
        $invite->setPrenom($data->get('prenom'));
        $invite->setEmail("");
        $invite->setAdresse($data->get('adresse'));
        $invite->setTelephone(str_replace(' ', '', $data->get('telephone')));
        $invite->setSituation($data->get('situation'));
        if (!empty($data->get("image"))){
            $img=$request->files->get("image");
            $imageName=uniqid().'.'.$img->guessExtension();
            $img->move($this->getParameter("profile"),$imageName);
            $invite->setPhoto($imageName);
        }
        if (!empty($data->get('hername'))){
            $invite->setHerName($data->get('hername'));
        } else {
            $invite->setHerName(null);
        }
        $this->em->persist($invite);

        if ($data->get('type')=="VIRTUEL"){
            $invite->setType($data->get('type'));
            $invite->setPlace(null);
            $this->em->persist($invite);

            //verifier si c'était un couple
            if ($invite->getHerPlace()){
                //supression de la place du conjoint
                $this->em->remove($invite->getHerPlace());
            }

        } else {
            //verie si c'est un invite physique
            $invite->setType("PHYSIQUE");
            $this->generateQrCode($invite->getSlug());
            $tab = $this->tableRipo->findOneBy(['slug'=>$data->get('type')]);
            $invite->setPlace($tab);
            $this->em->persist($invite);

            //verifie si il a un counjoint
            if (!empty($data->get('hername'))){

                //on verifie si le conjoint avait deja une place sinon on lui en crée
                if (!$invite->getHerPlace()){

                    //vérifie le nombre de place disponible
                    if ((count($tab->getHerPlaces()) + count($tab->getInvites()) )>8)
                    {
                        $this->addFlash('table_waring', 'Cette table ne peut pas accueillir un couple');
                        return $this->redirectToRoute('admin');
                    } else
                    {
                        $hp = (new HerPlace())
                            ->setInvite($invite)
                            ->setPlace($tab);
                        $this->em->persist($hp);
                    }
                }

            }
            //le champ du conjoint est vide donc on supprime la place que la personne occupait après avoir testé
            else {
                if ($invite->getHerPlace()) {
                    $this->em->remove($this->hpRipo->findOneBy(['invite'=>$invite]));
                    $invite->setHerName(null);
                }
            }
        }

        $this->em->flush();
        return $this->redirectToRoute('admin');
    }

    public function generateQrCode($slug){
        //ajout du code qr
        $writer = new PngWriter();
        $qrCode = QrCode::create($_SERVER['HTTP_HOST'].'/informations/'.$slug)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize(365)
            ->setMargin(10)
            ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        $result = $writer->write($qrCode);
        $result->saveToFile($this->getParameter("qr").'/'.$slug.'.png');

    }

    #[Route('/send/invitation', name:'send_invits')]
    public function sendInvits(){
        set_time_limit(1850);
        $invits = $this->inviteRipo->findAll();
        $api = new WhatsAppApi();
        foreach ($invits as $invit){

            if (!$invit->getInvitationsEnvoye()){
                if ($invit->getType()=='VIRTUEL') {
                    //$mail->send($invit->getEmail(), $this->reunion->getUrl(), $this->reunion->getPassword());
                    $url = $this->reunion->getUrl();
                    $password = $this->reunion->getPassword();
                    $msg = "Bonjour, compte tenu de votre indisponibilité, nous vous invitons à suivre notre maniage sur le lien suivant : $url %0A mot de passe : $password ";
                    $api->text($invit->getTelephone(), $this->messageText($msg));
                    //un invite virtuel n'a pas la possibilité de valider son
                    $invit->setValide(true);
                    $this->em->persist($invit);
                } else {
                    //$mail->physique($invit->getEmail(), 'link');
                    $link = $_SERVER['HTTP_HOST'].'/invitation/'.$invit->getSlug();
                    $msg = "Bonjour, nous vous invitons à confirmer votre présence à notre mariage en vous rendant sur ce lien : $link %0A %0A ce lien est unique et vous ne pourez confirmer votre présence qu'une fois";
                    $api->text($invit->getTelephone(), $this->messageText($msg));
                }
                $sent = (new InvitationsEnvoye())
                    ->setInvite($invit);
                $this->em->persist($sent);
            }
            sleep(6);
        }
        $this->em->flush();
        return $this->redirectToRoute('admin');
    }

    #[Route('/send/invitation/{slug}', name:'send_one_invit')]
    public function sendOneInvit($slug, Request $request){
        $api = new WhatsAppApi();
        $invit = $this->inviteRipo->findOneBy(['slug'=>$slug]);
        if ($invit->getType()=='VIRTUEL') {
           
            $url = $this->reunion->getUrl();
            $password = $this->reunion->getPassword();
            $msg = "Bonjour, compte tenu de votre indisponibilité, nous vous invitons à suivre notre maniage sur le lien suivant : $url %0A mot de passe : $password ";
            
            $api->text($invit->getTelephone(), $this->messageText($msg));
            $invit->setValide(true);
        } else {
            
            $link = $_SERVER['HTTP_HOST'].'/invitation/'.$invit->getSlug();
            $msg = "Bonjour, nous vous invitons à confirmer votre présence à notre mariage en vous rendant sur ce lien : $link %0A %0A ce lien est unique et vous ne pourez confirmer votre présence qu'une fois";
            
            $api->text($invit->getTelephone(), $this->messageText($msg));
        }
        $sent = (new InvitationsEnvoye())
            ->setInvite($invit);

        $this->em->persist($sent);
        $this->em->persist($invit);
        $this->em->flush();
        return $this->redirect($request->headers->get('referer'));
    }

    public function createAdmin($nom, $prenom, $mail, $password){
        $user = (new User())
            ->setPrenom($prenom)
            ->setNom($nom)
            ->setEmail($mail)
            ->setRoles(['ROLE_ADMIN']);
        $user->setPassword($this->encoder->hashPassword($user, $password));
        $this->em->persist($user);
        $this->em->flush();
        
    }

    public function messageText($text){
        $msg = str_replace(' ', '%20', $text);
        $msg = str_replace('/', '%2F',$msg);
        return $msg;
    }
}

<?php

namespace App\Controller;

use App\Entity\Invite;
use App\Entity\Table;
use App\Repository\DemandeRepository;
use App\Repository\InviteRepository;
use App\Repository\SalleRepository;
use App\Repository\TableRepository;
use Doctrine\ORM\EntityManagerInterface;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\Label\Label;
use Endroid\QrCode\Logo\Logo;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/admin')]
class AdminController extends AbstractController
{
    private SalleRepository $salleRipo;
    private TableRepository $tableRipo;
    private EntityManagerInterface $em;
    private InviteRepository $inviteRipo;
    private DemandeRepository $demandeRipo;

    public function __construct(
        SalleRepository $salleRipo,
        EntityManagerInterface $em,
        TableRepository $tableRipo,
        InviteRepository $inviteRipo,
        DemandeRepository $demandeRipo
    )
    {
        $this->salleRipo = $salleRipo;
        $this->tableRipo = $tableRipo;
        $this->inviteRipo = $inviteRipo;
        $this->em = $em;
        $this->salle = $salleRipo->find(1);
        $this->demandes = $demandeRipo->findBy(['etat'=>false]);
        $this->demandeRipo = $demandeRipo;
    }


    #[Route('', name: 'admin')]
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'salle'=>$this->salle->getNom(),
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
        $invite->setEmail($data->get('email'));
        $invite->setAdresse($data->get('adresse'));
        $invite->setTelephone($data->get('telephone'));
        $invite->setSituation($data->get('situation'));
        $invite->setValide(false);

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
            $invite->setType("PHYSIQUE");
            $invite->setPlace($this->tableRipo->findOneBy(['slug'=>$data->get('type')]));
        }
        $this->em->persist($invite);
        $this->em->flush();
        return $this->redirectToRoute('admin');
    }

    #[Route('/invite/{slug}', name:'dtl_invite')]
    public function dtlInvite($slug){
        return $this->render('admin/index.html.twig', [
            'salle'=>$this->salle->getNom(),
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

        $invite->setNom($data->get('nom'));
        $invite->setPrenom($data->get('prenom'));
        $invite->setEmail($data->get('email'));
        $invite->setAdresse($data->get('adresse'));
        $invite->setTelephone($data->get('telephone'));
        $invite->setSituation($data->get('situation'));

        if ($data->get('type')=="VIRTUEL"){
            $invite->setType($data->get('type'));
            $invite->setPlace(null);
        } else {
            $invite->setType("PHYSIQUE");
            $this->generateQrCode($invite->getSlug());
            $invite->setPlace($this->tableRipo->findOneBy(['slug'=>$data->get('type')]));
        }

        if (!empty($data->get("image"))){
            $img=$request->files->get("image");
            $imageName=uniqid().'.'.$img->guessExtension();
            $img->move($this->getParameter("profile"),$imageName);
            $invite->setPhoto($imageName);
        }

        $this->em->persist($invite);
        $this->em->flush();
        return $this->redirectToRoute('admin');
    }

    public function generateQrCode($slug){
        //ajout du code qr
        $writer = new PngWriter();
        $qrCode = QrCode::create('https://www.jordan.com/profile/'.$slug)
            ->setEncoding(new Encoding('UTF-8'))
            ->setErrorCorrectionLevel(new ErrorCorrectionLevelLow())
            ->setSize(300)
            ->setMargin(10)
            ->setRoundBlockSizeMode(new RoundBlockSizeModeMargin())
            ->setForegroundColor(new Color(0, 0, 0))
            ->setBackgroundColor(new Color(255, 255, 255));

        $result = $writer->write($qrCode);
        $result->saveToFile($this->getParameter("qr").'/'.$slug.'.png');

    }
}

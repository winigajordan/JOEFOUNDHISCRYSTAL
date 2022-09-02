<?php

namespace App\Controller;

use App\Entity\Demande;
use App\Entity\HerPlace;
use App\Entity\Invite;
use App\Repository\DemandeRepository;
use App\Repository\TableRepository;
use Doctrine\ORM\EntityManagerInterface;
use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode\RoundBlockSizeModeMargin;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
#[Route('/registration')]
class DemandeInvitationController extends AbstractController
{
    #[Route('/invitation/{etat}', name: 'app_demande_invitation', defaults: ['etat'=>'single'])]
    public function index($etat): Response
    {
        return $this->render('demande_invitation/index.html.twig', [
            'etat'=>$etat
        ]);
    }

    #[Route('/add', name: 'demande_invitation')]
    public function demandeAdd(Request $request, EntityManagerInterface $em): Response
    {
        $demande = $this->generateDemande($request);
        $em->persist($demande);
        $em->flush();
        return $this->redirectToRoute('app_demande_invitation');
    }

    #[Route('/add/couple', name: 'couple_invitation')]
    public function demandeCoupleAdd(Request $request, EntityManagerInterface $em): Response
    {
        $demande = $this->generateDemande($request);
        $demande->setHerName($request->request->get('herName'));
        $em->persist($demande);
        $em->flush();
        return $this->redirectToRoute('app_demande_invitation');
    }

    #[Route('/traitement', name: 'demande_traitement', methods: 'POST')]
    public function update(Request $request, DemandeRepository $demandeRipo, TableRepository $tableRipo, EntityManagerInterface $em){
        $demande = $demandeRipo->findOneBy(['slug'=>$request->request->get('slug')]);
        if ($request->request->get("type")!="ANNULER"){
            $invite = (new Invite())
                ->setCivilite($demande->getCivilite())
                ->setSlug(uniqid('invit-'))
                ->setSituation($demande->getSituation())
                ->setTelephone($demande->getTelephone())
                ->setEmail($demande->getEmail())
                ->setNom($demande->getNom())
                ->setPrenom($demande->getPrenom())
                ->setPhoto($demande->getImage())
                ->setAdresse('adresse')
                ->setValide(false);

            if ($demande->getHerName()){
                $invite->setHerName($demande->getHerName());
            }

            if ($request->request->get('type')=="VIRTUEL"){
                $invite->setType($request->request->get('type'));
            } else {
                $tab = $tableRipo->findOneBy(['slug'=>$request->request->get('type')]);
                $invite->setType("PHYSIQUE");
                if ($demande->getHerName()){
                    $hp = new HerPlace();
                    $hp->setPlace($tab);
                    $hp->setInvite($invite);
                    $em->persist($hp);
                }
                $invite->setPlace($tab);
            }
            $this->generateQrCode($invite->getSlug());
            $demande->setEtat(true);
            $em->persist($demande);
            $em->persist($invite);
            $em->flush();
        }
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


    public function generateDemande(Request $request): Demande
    {
        $data = $request->request;
        $demande = (new Demande())
            ->setCivilite($data->get('civilite'))
            ->setNom($data->get('nom'))
            ->setPrenom($data->get('prenom'))
            ->setEmail('')
            ->setTelephone(str_replace(' ', '', $data->get('telephone')))
            ->setSituation($data->get('situation'))
            ->setSlug(uniqid('dmd-'))
            ->setEtat(false);

        //ajout d'image
        $img = $request->files->get("image");
        $imageName = $demande->getSlug() . '.' . $img->guessExtension();
        $img->move($this->getParameter("profile"), $imageName);
        $demande->setImage($imageName);
        return $demande;
    }
}

<?php

namespace HistoriqueGlobalBundle\Controller;

use EmployeBundle\Entity\Societe;
use PaieBundle\Entity\Historique_AvanceSpecial;
use PaieBundle\Entity\Mois;
use PaieBundle\Entity\Numero_demande;
use PresenceBundle\Services\PresenceSpService;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use PaieBundle\Entity\Historique_Paie;


class AccueilController extends Controller
{

    private function nouveauMois(){

        $em = $this->getDoctrine()->getManager();

        //------------------RESAKA PAIE-----------

        $employes = $em->getRepository('EmployeBundle:Employe')->findBy(
            array(
                'etat' => true
            )
        );

        foreach ($employes as $employe)
        {
            $histo_paie=new Historique_Paie();
            $histo_paie->setDate( new \DateTime());
            $histo_paie->setLibelle("Salaire de base");
            $histo_paie->setType("credit");
            $histo_paie->setSalaireBase(true);
            $histo_paie->setComptePaie($employe->getComptePaie());
            $histo_paie->setMontant($employe->getSalaire());


            $em->persist($histo_paie);


            //------------------RESAKA RH PRESENCE-----------
            $comptePresence = $employe->getComptePresence();
            $comptePresence->setResteConge($comptePresence->getResteConge() + 2.5);

            $em->persist($comptePresence);

            //------------------/////////RESAKA RH PRESENCE/////////-----------

        }

        //-------------------------AVANCE SPECIAL-------------
        $avanceSpecials = $em->getRepository('PaieBundle:Avance_Special')->findBy(array(
            'etat' => true
        ));

        foreach ($avanceSpecials as $avanceSpecial)
        {
            $reste = $avanceSpecial->getMontant() - $avanceSpecial->getSommePaier();
            if($reste <= 0 or $reste <= 1){
                $avanceSpecial->setEtat(false);
            }else{
                if($avanceSpecial->getParMois() != 0){
                    $aPaier = $avanceSpecial->getParMois();
                    if($reste < $avanceSpecial->getParMois())
                        $aPaier = $reste
                    ;

                    $avanceSpecial->setSommePaier($avanceSpecial->getSommePaier() + $aPaier);

                    $histo_paie=new Historique_Paie();
                    $histo_paie->setComptePaie($avanceSpecial->getEmpoye()->getComptePaie());
                    $histo_paie->setMontant($aPaier);
                    $histo_paie->setType("debit");
                    $histo_paie->setButtonModif(true);
                    $histo_paie->setLibelle("Remboursement Avance special");
                    $histo_paie->setDate(new \DateTime());
                    $em->persist($histo_paie);

                    $histo_av = new Historique_AvanceSpecial();

                    $histo_av->setMontant($avanceSpecial->getParMois());
                    $histo_av->setDate($histo_paie->getDate());
                    $histo_av->setAvanceSpecial($avanceSpecial);
                    $histo_av->setHistoriquePaie($histo_paie);
                    $em->persist($histo_av);

                }
            }

            $em->persist($avanceSpecial);
        }
        $em->flush();
        //-------------------------//////AVANCE SPECIAL//////-------------

        //------------------///////////RESAKA PAIE///////////-----------
    }

    //---------FONCTION MANUEL-------------
    public function updateAvanceSpecialAction(){
        $em = $this->getDoctrine()->getManager();

        //-------------------------AVANCE SPECIAL-------------
        $avanceSpecials = $em->getRepository('PaieBundle:Avance_Special')->findBy(array(
            'etat' => true
        ));

        foreach ($avanceSpecials as $avanceSpecial)
        {
            if($avanceSpecial->getParMois() != 0){
                $reste = $avanceSpecial->getMontant() - $avanceSpecial->getSommePaier();
                $aPaier = $avanceSpecial->getParMois();
                if($reste < $avanceSpecial->getParMois())
                    $aPaier = $reste;

                $avanceSpecial->setSommePaier($avanceSpecial->getSommePaier() + $aPaier);

                $histo_paie=new Historique_Paie();
                $histo_paie->setComptePaie($avanceSpecial->getEmpoye()->getComptePaie());
                $histo_paie->setMontant($aPaier);
                $histo_paie->setType("debit");
                $histo_paie->setLibelle("Remboursement Avance special");
                $histo_paie->setDate(new \DateTime());
                $em->persist($histo_paie);

                $histo_av = new Historique_AvanceSpecial();

                $histo_av->setMontant($avanceSpecial->getParMois());
                $histo_av->setDate($histo_paie->getDate());
                $histo_av->setAvanceSpecial($avanceSpecial);
                $em->persist($histo_av);

                $em->persist($avanceSpecial);
            }

        }
        $em->flush();
        //-------------------------//////AVANCE SPECIAL//////-------------

        //------------------///////////RESAKA PAIE///////////-----------

        return new Response('Update OKEY');

    }

    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();

        //        --------------------TEST DU MOIS EN COUR--------------------
        $mois=$em->getRepository('PaieBundle:Mois')->findOneBy(array('nom_config'=>'ma_date'));
        $moisActuel = new \DateTime();
        if($mois){
            $moisEncours = $mois->getMoisencours();

            if($moisEncours->format('m/Y') != $moisActuel->format('m/Y') and $moisEncours < $moisActuel){

                $this->nouveauMois();
                $mois->setMoisencours($moisActuel);
                $em->persist($mois);
            }
        }
        else{
            $nouveau_mois = new Mois();
            $nouveau_mois->setMoisencours($moisActuel);
            $nouveau_mois->setNomConfig('ma_date');
            $em->persist($nouveau_mois);
        }
        //        --------------------//////////TEST DU MOIS EN COUR//////////--------------------


//        ----------------TEST NUMERO DEMANDE D'AVANCE----------------

        $repository_numAvance = $em->getRepository('PaieBundle:Numero_demande');

        $numero = $repository_numAvance->findOneBy(array('nom_config' => 'numero_demande_avance'));
        $numero_avance_special = $repository_numAvance->findOneBy(array('nom_config' => 'numero_demande_avance_special'));

        if(!$numero){
            $new_num = new Numero_demande();
            $new_num->setNomConfig('numero_demande_avance');
            $new_num->setNumeroDemande('0001');
            $em->persist($new_num);
        }

        if(!$numero_avance_special){
            $new_num = new Numero_demande();
            $new_num->setNomConfig('numero_demande_avance_special');
            $new_num->setNumeroDemande('0001');
            $em->persist($new_num);
        }

//        ----------------////////TEST NUMERO DEMANDE D'AVANCE////////----------------

        //        ----------------TEST DES SOCIETE----------------

        $repository_societe = $em->getRepository('EmployeBundle:Societe');

        $societes = $repository_societe->findAll();

        if(count($societes) <= 0 ){
            $fatemi = new Societe();
            $fatemi->setNom('TR-FATEMI');
            $fatemi->setAdresse('6, Làlana Karijà, Tsaralalàna');
            $fatemi->setContact('020 22 279 23');
            $fatemi->setNif('5000347967');
            $fatemi->setStat('46101 11 1974 0 00098');

            $em->persist($fatemi);

            $tanisoa = new Societe();
            $tanisoa->setNom('TANISOA');
            $tanisoa->setAdresse('Lot VM 98 CA Bis Androndrakely - Rue Ramilijaona Ankadimbahoaka');
            $tanisoa->setContact('020 22 279 23');
            $tanisoa->setNif('30001171902');
            $tanisoa->setStat('46101 11 1989 0 10025');

            $em->persist($tanisoa);

            $tamatrade = new Societe();
            $tamatrade->setNom('TAMATARDE');
            $tamatrade->setAdresse('29 rue Amiral billard - "Noman Center"');
            $tamatrade->setContact('034 69 384 19');
            $tamatrade->setNif('5002627394');
            $tamatrade->setStat('45302 31 2013 0 00106');

            $em->persist($tamatrade);

            $sogeoi = new Societe();
            $sogeoi->setNom('SOGEOI');
            $sogeoi->setAdresse('29 rue amiral billard Toamasina');
            $sogeoi->setContact('020 22 279 23');
            $sogeoi->setNif('400206758');
            $sogeoi->setStat('47521 31 2015 000651');

            $em->persist($sogeoi);


        }

//        ----------------////////TEST DES SOCIETE////////----------------

        $servIP = $this->getRequest()->server->get('SERVER_ADDR');
        $em->flush();

        return $this->render('HistoriqueGlobalBundle:Default:index.html.twig', array(
            'servIP' => $servIP
        ));
    }

    function randomLettre() {
        $len = 5;
        $chars = 'ABCDEFGHIJK';
        $randnb = '';
        for ($i = 0; $i < $len; ++$i)
            $randnb .= substr($chars, (mt_rand() % strlen($chars)), 1);
        return $randnb;
    }

    public function testAction()
    {

//        $tmp = 3;
//        if (is_float($tmp)){
//            return new Response(number_format($tmp, 1, ',', ' '));
//        }else{
//            return new Response($tmp);
//        }

        $em = $this->getDoctrine()->getManager();

        $repository_employe = $em->getRepository('EmployeBundle:Employe');

        $employes = $repository_employe->findBy(array(
            'etat' => true
        ));

        $dateFin = \DateTime::createFromFormat('d/m/Y', '31/01/2020');

        foreach ($employes as $employe){

            $compte_Paie = $employe->getComptePaie();

            $tmpSalaireBase = new Historique_Paie();
            $tmpSalaireBase->setType('tmp_base');
            $tmpSalaireBase->setLibelle('Salaire de base');
            $tmpSalaireBase->setDate($dateFin);
            $tmpSalaireBase->setComptePaie($compte_Paie);
            $tmpSalaireBase->setMontant($employe->getSalaire());

            $em->persist($tmpSalaireBase);

            $tmpNombreEnfant = new Historique_Paie();
            $tmpNombreEnfant->setType('tmp_nbEnfant');
            $tmpNombreEnfant->setLibelle('Nombre enfant');
            $tmpNombreEnfant->setDate($dateFin);
            $tmpNombreEnfant->setComptePaie($compte_Paie);
            if($employe->getNbEnfant()){
                $tmpNombreEnfant->setMontant($employe->getNbEnfant());
            }else{
                $tmpNombreEnfant->setMontant(0);
            }

            $em->persist($tmpNombreEnfant);

            $minimumPerception = new Historique_Paie();
            $minimumPerception->setType('tmp_minimumPerception');
            $minimumPerception->setLibelle('Minimum perception');
            $minimumPerception->setDate($dateFin);
            $minimumPerception->setComptePaie($compte_Paie);
            $minimumPerception->setMontant(2000);


            $em->persist($minimumPerception);
        }

        $em->flush();

        return new Response("Mis à jour OKEY!!");

    }

    private function calculHeure($heureSupps){
        $totalHeure = 0;

        foreach ($heureSupps as $heureSupp){
            $totalHeure = $totalHeure + $heureSupp['heure'];
        }

        return $totalHeure;
    }

    public function test2Action()
    {
        return new Response('TEST');
    }
}

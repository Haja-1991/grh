<?php

namespace PaieBundle\Controller;


use PaieBundle\Services\ChiffreEnLettreService;
use PaieBundle\Services\PaieService;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

use PaieBundle\Entity\Compte_Paie;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use EmployeBundle\Entity\Employe;

use PaieBundle\Entity\Historique_Paie;
use PaieBundle\Entity\Demande_Avance;

/**
 * Paie controller.
 *
 * @Route("/")
 */
class comptePaieController extends Controller
{

    /**
     * Lists all Employe entities.
     *
     * @Route("/", name="paie_homepage")
     * @Method("GET")
     */
    public function afficherCompteAction()
    {
        if(!$this->getUser()->ifRole('ROLE_PAIE')) return new Response('404 NOT-FOUND');

        $em = $this->getDoctrine()->getManager();


        $employes = $em->getRepository('EmployeBundle:Employe')->findBy(array('etat'=>true));

        return $this->render('@Paie/paie/index.html.twig', array(
            'employes' => $employes,
        ));

    }
    /**
     * Lists all Employe entities.
     *
     * @Route("/detail/{slug}", name="paie_detail")
     * @Method("GET")
     */
    public function paieDetailAction(Compte_Paie $compte_Paie)
    {
        $em = $this->getDoctrine()->getManager();

        $repository_historiquePaie = $em->getRepository('PaieBundle:Historique_Paie');

        $employe = $em->getRepository('EmployeBundle:Employe')->find($compte_Paie);

        $dateActuel = new \DateTime();

        $mois = $dateActuel->format('m');
        $annee = $dateActuel->format('Y');

        if (isset($_GET['mois']) and isset($_GET['année'])){
            $mois = $_GET['mois'];
            $annee = $_GET['année'];
        }



        $date_test = mktime( 0, 0, 0, $mois, 1, $annee );
        $fin_mois = date('t', $date_test) + 1;
        $servIP = $this->getRequest()->server->get('SERVER_ADDR');
        $dateDebut = \ DateTime::createFromFormat('d/m/Y', '1/'.$mois.'/'.$annee)->format('Y-m-d');
        $dateFin = \ DateTime::createFromFormat('d/m/Y', $fin_mois.'/'.$mois.'/'.$annee)->format('Y-m-d');

        $historique_paie = $repository_historiquePaie->findByMoisAnne($compte_Paie->getSlug(), $dateDebut, $dateFin);

        $verification = false;
        $reste_conge="";
        foreach ($historique_paie as $historique){
            if($historique->getVerification()){
                $verification = true;
            }
            if($historique->getType()=='tmp_reste_conge'){
                $reste_conge=$historique;
            }
        }

        if(isset($_GET['global'])){
            $historique_paie = $repository_historiquePaie->findBy(
                array('comptePaie' => $compte_Paie),
                array('date' => 'asc')
            );

            return $this->render('@Paie/paie/paieDetailGlobal.html.twig',array(
                'employe' => $employe,
                'compte_Paie' => $compte_Paie,
                'historique' => $historique_paie,
                'mois' => $mois,
                'annee' => $annee,
                'servIP' => $servIP
            ));
        }

        return $this->render('@Paie/paie/paieDetail.html.twig',array(
            'employe' => $employe,
            'compte_Paie' => $compte_Paie,
            'historiques' => $historique_paie,
            'mois' => $mois,
            'annee' => $annee,
            'servIP' => $servIP,
            'verification' => $verification,
            'resteConge'=>$reste_conge,
        ));

    }

    /**
     * IMPRESSION TEST FICHE DE PAIE
     *
     * @Route("/p/{slug}/fiche-de-paie", name="paie_fiche_test")
     * @Method("GET")
     */
    public function testImpressionAction(Compte_Paie $compte_Paie){

        $em = $this->getDoctrine()->getManager();

        $repository_historiquePaie = $em->getRepository('PaieBundle:Historique_Paie');

        $employe = $em->getRepository('EmployeBundle:Employe')->find($compte_Paie);

        if (isset($_GET['mois']) and isset($_GET['année'])){
            $mois = $_GET['mois'];
            $annee = $_GET['année'];
        }else {
            return new Response('404 NOT-FOUND');
        }

//         ------------------- RESTE AVANCE SPECIAL -------------------

        $repositoryAvanceSp = $em->getRepository('PaieBundle:Avance_Special');
        $avanceSpActives = $repositoryAvanceSp->findBy(array(
            'empoye' => $employe,
            'etat' => true
        )) ;

        $resteAvanceSpecial = null;

        if (count($avanceSpActives) > 0){
            $resteAvcSpMontant = 0;
            $resteAvcSpMois = 0;
            foreach ($avanceSpActives as $avanceSpActive){
                $resteMontant = $avanceSpActive->getMontant() - $avanceSpActive->getSommePaier();
                $resteAvcSpMontant = $resteAvcSpMontant + $resteMontant;

                $resteTranche = null;
                if ($avanceSpActive->getParMois() == 0)
                    $resteTranche = $resteMontant / 1;
                else
                    $resteTranche = $resteMontant / $avanceSpActive->getParMois();

                if ($resteTranche >= $resteAvcSpMois)
                    $resteAvcSpMois = $resteTranche;
            }

            $resteAvanceSpecial = array(
                'montant' => $resteMontant,
                'mois' => $resteAvcSpMois
            );
        }


//         ------------------- /*///RESTE AVANCE SPECIAL/*/// -------------------
//


        $date_test = mktime( 0, 0, 0, $mois, 1, $annee );
        $fin_mois = date('t', $date_test) + 1;

        $date_Debut = \ DateTime::createFromFormat('d/m/Y', '1/'.$mois.'/'.$annee);
        $date_Fin = \ DateTime::createFromFormat('d/m/Y', $fin_mois.'/'.$mois.'/'.$annee);

        $dateDebut = $date_Debut->format('Y-m-d');
        $dateFin = $date_Fin->format('Y-m-d');

        $historique_paies = $repository_historiquePaie->findByMoisAnne($compte_Paie->getSlug(), $dateDebut, $dateFin);

        $salaireBase = null;
        $allocConge = null;
        $heureSuppExonerer = null;
        $heureSuppDeductible = null;
        $avanceQz = 0;
        $avanceSpecial = 0;
        $ostie = null;
        $cnaps = null;
        $irsa = null;
        $autreDeductions = array();
        $autreCredits = array();
        $tmpSalaireBase = null;
        $tmpNbEnfant = null;
        $tmpNbConge = '-';

        foreach ($historique_paies as $historique_paie){
//            $historique_paie = new Historique_Paie(); // ---------------- A COMMENTER --------------

            $historique_speciale = $em->getRepository('PaieBundle:Historique_AvanceSpecial')->findOneBy(array('historique_paie'=>$historique_paie));

            // -------------------------------------------

            if($historique_paie->getSalaireBase())
                $salaireBase = $historique_paie;

            if ($historique_paie->getType() == "alloc_final")
                $allocConge = $historique_paie;

            if ($historique_paie->getType() == "exonerer")
                $heureSuppExonerer = $historique_paie;

            if ($historique_paie->getType() == "deductible")
                $heureSuppDeductible = $historique_paie;

            if($historique_paie->getType() == "tmp_base"){
                $tmpSalaireBase = $historique_paie;
            }

            if($historique_paie->getType() == "tmp_nbEnfant"){
                $tmpNbEnfant = $historique_paie;
            }

            if($historique_paie->getType() == "tmp_reste_conge"){
                if($historique_paie->getMontant() > 0){
                    $tmpNbConge = $historique_paie->getMontant();
                }
            }

            if ($historique_paie->getType() == "debit"){
                if(strpos($historique_paie->getLibelle(), 'Avance sur salaire') !== false){
                    $avanceQz += $historique_paie->getMontant();
                }
                elseif(strpos($historique_paie->getLibelle(), 'Remboursement Avance') !== false or $historique_speciale){
                    $avanceSpecial += $historique_paie->getMontant();
                }
                elseif($historique_paie->getOstie()){
                    $ostie = $historique_paie;
                }
                elseif($historique_paie->getCnaps()){
                    $cnaps = $historique_paie;
                }
                elseif($historique_paie->getIrsa()){
                    $irsa = $historique_paie;
                }else {
                    if ($historique_paie->getType() == 'debit' and !$historique_paie->getPriseSalaire()) {
                        array_push($autreDeductions, $historique_paie);
                    }
                }
            }

            if ($historique_paie->getType() == "credit" and ! $historique_paie->getSalaireBase())
                array_push($autreCredits, $historique_paie)
            ;
        }

        $totalCredit = $salaireBase->getMontant();

        if($allocConge)
            $totalCredit += $allocConge->getMontant();

        if($heureSuppExonerer)
            $totalCredit += $heureSuppExonerer->getMontant();

        if($heureSuppDeductible)
            $totalCredit += $heureSuppDeductible->getMontant();

        foreach ($autreCredits as $autreCredit){
            $totalCredit += $autreCredit->getMontant();
        }

        $totalDebit = 0;
        foreach ($autreDeductions as $autreDeduction){
            $totalDebit += $autreDeduction->getMontant();
        }
        if($ostie)
            $totalDebit += $ostie->getMontant();

        if($cnaps)
            $totalDebit += $cnaps->getMontant();

        if($irsa)
            $totalDebit += $irsa->getMontant();


        $totalDebit = $totalDebit + $avanceQz + $avanceSpecial;

        $chiffreEnLettre = new ChiffreEnLettreService();
        $paieService = new PaieService();


        $netTotalFloat = $totalCredit - $totalDebit;

        $netTotalArround = $paieService->special_round($netTotalFloat, 100);
        $netEnLettre = $chiffreEnLettre->Conversion($netTotalArround);


        return $this->render('@Paie/paie/fiche_paie.html.twig',array(
            'employe' => $employe,
            'compte_Paie' => $compte_Paie,
            'mois' => $mois,
            'annee' => $annee,
            'date_Debut' => $date_Debut,
            'date_Fin' => $date_Fin,
            'salaireBase' => $salaireBase,
            'allocConge' => $allocConge,
            'heureSuppExonerer' => $heureSuppExonerer,
            'heureSuppDeductible' => $heureSuppDeductible,
            'avanceQz' => $avanceQz,
            'avanceSp' => $avanceSpecial,
            'ostie' => $ostie,
            'cnaps' => $cnaps,
            'irsa' => $irsa,
            'autreDeduction' => $autreDeductions,
            'autreCredits' => $autreCredits,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
            'netTotalLettre' => $netEnLettre,
            'tmpSalaireBase' => $tmpSalaireBase,
            'tmpNbEnfant' => $tmpNbEnfant,
            'tmpNbConge' => $tmpNbConge,
            'resteAvanceSpecial' => $resteAvanceSpecial,
            'netTotalArround' => $netTotalArround
        ));
    }


    /**
     * IMPRESSION TEST FICHE DE PAIE
     *
     * @Route("/reste-salaire/employes", name="paie_reste_salaire")
     * @Method("GET")
     */
    public function resteSalaireEmployeAction(){

        $em = $this->getDoctrine()->getManager();

        $dateActuel = new \DateTime();
        $mois = $dateActuel->format('m');
        $annee = $dateActuel->format('Y');

        if (isset($_GET['mois']) and isset($_GET['année'])){
            $mois = $_GET['mois'];
            $annee = $_GET['année'];
        }

        $date_test = mktime( 0, 0, 0, $mois, 1, $annee );
        $fin_mois = date('t', $date_test) + 1;


        $date_Debut = \ DateTime::createFromFormat('d/m/Y h:i', '1/'.$mois.'/'.$annee.' 00:00');
        $date_Fin = \ DateTime::createFromFormat('d/m/Y', $fin_mois.'/'.$mois.'/'.$annee);

        $dateTestEmploye =  \ DateTime::createFromFormat('d/m/Y h:i', ($fin_mois-1).'/'.$mois.'/'.$annee.' 00:00');

        $dateDebut = $date_Debut->format('Y-m-d');
        $dateFin = $date_Fin->format('Y-m-d');

        $listes = array();

        $repository_employe = $em->getRepository('EmployeBundle:Employe');
        $repository_historiquePaie = $em->getRepository('PaieBundle:Historique_Paie');

        $emps = $repository_employe->findAll();

        $employes = array();

        // ------------------- MILA ANNDRAMANA IO AMBANY IO !!!!!!!!!!!!!!!!!!!!!!!!!!!! ---------------------

        foreach ($emps as $emp){
            if($emp->getEtat() and $emp->getDateDebut() < $dateTestEmploye){
                array_push($employes, $emp);
            }elseif (($emp->getDateDebauche() >= $date_Debut and $emp->getDateDebauche()) )
                array_push($employes, $emp);


        }

        // ------------------- ////// MILA ANNDRAMANA IO AMBANY IO !!!!!!!!!!!!!!!!!!!!!!!!!!!! ////// ---------------------



        foreach ($employes as $employe){
//            $employe = new Employe(); // --------  A COMMENTER ----------

            $entite = array();
            $entite['matricule'] = $employe->getMatricul();
            $entite['nom'] = $employe->getNom();
            $entite['prenom'] = $employe->getPrenom();
            $entite['poste'] = $employe->getPoste();
            $entite['nbEnfant'] = $employe->getNbEnfant();
            $entite['empSalaire'] = $employe->getSalaire();
            $entite['slug'] = $employe->getComptePaie()->getSlug();
            $entite['methodePaiement'] = $employe->getMethodePaiement();

            $compte_Paie = $employe->getComptePaie();

            $historique_paies = $repository_historiquePaie->findByMoisAnne($compte_Paie->getSlug(), $dateDebut, $dateFin);

            $salaireBase = null;
            $allocConge = null;
            $heureSuppExonerer = null;
            $heureSuppDeductible = null;
            $avanceQz = 0;
            $avanceSpecial = 0;
            $ostie = null;
            $cnaps = null;
            $irsa = null;
            $autreDeductions = array();
            $autreCredits = array();
            $tmpNbEnfant = null;
            $tmpSalaireBase = null;

            foreach ($historique_paies as $historique_paie){
//            $historique_paie = new Historique_Paie(); // ---------------- A COMMENTER --------------

                $historique_speciale = $em->getRepository('PaieBundle:Historique_AvanceSpecial')->findOneBy(array('historique_paie'=>$historique_paie));

                // -------------------------------------------

                if($historique_paie->getSalaireBase())
                    $salaireBase = $historique_paie;

                if ($historique_paie->getType() == "alloc_final")
                    $allocConge = $historique_paie;

                if ($historique_paie->getType() == "exonerer")
                    $heureSuppExonerer = $historique_paie;

                if ($historique_paie->getType() == "deductible")
                    $heureSuppDeductible = $historique_paie;

                if($historique_paie->getType() == "tmp_base"){
                    $tmpSalaireBase = $historique_paie;
                }

                if($historique_paie->getType() == "tmp_nbEnfant"){
                    $tmpNbEnfant = $historique_paie;
                }

                if ($historique_paie->getType() == "debit"){
                    if(strpos($historique_paie->getLibelle(), 'Avance sur salaire') !== false){
                        $avanceQz += $historique_paie->getMontant();
                    }
                    elseif(strpos($historique_paie->getLibelle(), 'Remboursement Avance') !== false or $historique_speciale){
                        $avanceSpecial += $historique_paie->getMontant();
                    }
                    elseif($historique_paie->getOstie()){
                        $ostie = $historique_paie;
                    }
                    elseif($historique_paie->getCnaps()){
                        $cnaps = $historique_paie;
                    }
                    elseif($historique_paie->getIrsa()){
                        $irsa = $historique_paie;
                    }else {
                        if ($historique_paie->getType() == 'debit' and !$historique_paie->getPriseSalaire()) {
                            array_push($autreDeductions, $historique_paie);
                        }
                    }
                }

                if ($historique_paie->getType() == "credit" and ! $historique_paie->getSalaireBase())
                    array_push($autreCredits, $historique_paie)
                    ;
            }

            if (!$salaireBase){
                $salaireBase = new Historique_Paie();
                $salaireBase->setMontant($employe->getSalaire());
            }
            $totalCredit = $salaireBase->getMontant();

            if($allocConge)
                $totalCredit += $allocConge->getMontant();

            if($heureSuppExonerer)
                $totalCredit += $heureSuppExonerer->getMontant();

            if($heureSuppDeductible)
                $totalCredit += $heureSuppDeductible->getMontant();

            foreach ($autreCredits as $autreCredit){
                $totalCredit += $autreCredit->getMontant();
            }

            $totalDebit = 0;
            foreach ($autreDeductions as $autreDeduction){
                $totalDebit += $autreDeduction->getMontant();
            }
            if($ostie)
                $totalDebit += $ostie->getMontant();

            if($cnaps)
                $totalDebit += $cnaps->getMontant();

            if($irsa)
                $totalDebit += $irsa->getMontant();

            $totalDebit = $totalDebit + $avanceQz + $avanceSpecial;


            $entite['mois'] = $mois;
            $entite['annee'] = $annee;
            $entite['date_Debut'] = $date_Debut;
            $entite['date_Fin'] = $date_Fin;
            $entite['salaireBase'] = $salaireBase;
            $entite['allocConge'] = $allocConge;
            $entite['heureSuppExonerer'] = $heureSuppExonerer;
            $entite['heureSuppDeductible'] = $heureSuppDeductible;
            $entite['avanceQz'] = $avanceQz;
            $entite['avanceSp'] = $avanceSpecial;
            $entite['ostie'] = $ostie;
            $entite['cnaps'] = $cnaps;
            $entite['irsa'] = $irsa;
            $entite['autreDeduction'] = $autreDeductions;
            $entite['autreCredits'] = $autreCredits;
            $entite['totalDebit'] = $totalDebit;
            $entite['totalCredit'] = $totalCredit;
            $entite['tmpSalaireBase'] = $tmpSalaireBase;
            $entite['tmpNbEnfant'] = $tmpNbEnfant;

            //-------------- FIN ------------------
            array_push($listes, $entite);

        }

        if (isset($_GET['paiement'])){
            $mthd = $_GET['paiement'];
            $tmpListe = array();

            foreach ($listes as $liste){
                if($liste['methodePaiement'] == $mthd)
                    array_push($tmpListe, $liste);

                if ($mthd == 'Espèces' && ! $liste['methodePaiement']){
                    array_push($tmpListe, $liste);
                }
            }

            return $this->render('@Paie/paie/reste_salaire_duMois_exceptioin.twig', array(
                'listes' => $tmpListe,
                'mois' => $mois,
                'annee' => $annee,
                'paiement' => $mthd
            ));


        }

        return $this->render('@Paie/paie/reste_salaire_duMois.twig', array(
           'listes' => $listes,
            'mois' => $mois,
            'annee' => $annee,
        ));

    }

    /**
     * IMPRESSION TEST FICHE DE PAIE
     *
     * @Route("/tout/employes/deduire", name="paie_tout_deduire")
     * @Method("GET")
     */
    public function toutDeduireAction(){
        $em = $this->getDoctrine()->getManager();

        $repository_employe = $em->getRepository('EmployeBundle:Employe');

        $paieService = new PaieService();

        $employes = $repository_employe->findBy(array(
            'etat' => true
        ));


        $dateActuel = new \DateTime();
        $mois = $dateActuel->format('m');
        $annee = $dateActuel->format('Y');

        if (isset($_GET['mois']) and isset($_GET['année'])){
            $mois = $_GET['mois'];
            $annee = $_GET['année'];
        }

        $date_test = mktime( 0, 0, 0, $mois, 1, $annee );
        $fin_mois = date('t', $date_test) ;

        $dateFin = \ DateTime::createFromFormat('d/m/Y', $fin_mois.'/'.$mois.'/'.$annee);


        foreach ($employes as $employe){


            //TEST SI LE IRSA EXISTE DANS LA DATE
            if(! $paieService->testIrsa($em, $employe, $mois, $annee)){
                $salaire = $employe->getSalaire();

                if($employe->getSalaireMin())
                    $salaire = $employe->getSalaireMin();

                $compte_Paie = $employe->getComptePaie();

                $ostieCnaps = $salaire * 0.01;

                $ostie = new Historique_Paie();
                $ostie->setType('debit');
                $ostie->setLibelle('Cotisation OSTIE');
                $ostie->setDate($dateFin);
                $ostie->setOstie(true);
                $ostie->setComptePaie($compte_Paie);
                $ostie->setMontant($ostieCnaps);
                $ostie->setDate($dateFin);

                $em->persist($ostie);

                $cnaps = clone $ostie;
                $cnaps->setOstie(null);
                $cnaps->setCnaps(true);
                $cnaps->setLibelle('Cotisation CNaPS');

                $em->persist($cnaps);

                $revenuNet = $salaire - ($ostieCnaps * 2);

                $montantIrsa = 3000;

                if($revenuNet > 350000){
                    $montantIrsa = ($revenuNet - 350000) * 0.2;

                    if($employe->getNbEnfant()){
                        if($employe->getNbEnfant() > 0){
                            $montantIrsa = $montantIrsa - ($employe->getNbEnfant() * 2000);

                        }
                    }

                }

                $irsa = new Historique_Paie();
                $irsa->setType('debit');
                $irsa->setLibelle('Prélèvement IRSA');
                $irsa->setDate($dateFin);
                $irsa->setIrsa(true);
                $irsa->setComptePaie($compte_Paie);
                $irsa->setMontant($montantIrsa);
                $irsa->setDate($dateFin);

                $em->persist($irsa);
            }
        }

        $em->flush();

        return $this->redirectToRoute('paie_reste_salaire', array('mois' => $mois, 'année' => $annee));
    }


    /**
     * IMPRESSION TEST FICHE DE PAIE
     *
     * @Route("/imprimer/etat/salaire", name="paie_etat_imprimer")
     * @Method("GET")
     */
    public function imprimerEtatAction(){

        $em = $this->getDoctrine()->getManager();

        $dateActuel = new \DateTime();
        $mois = $dateActuel->format('m');
        $annee = $dateActuel->format('Y');

        if (isset($_GET['mois']) and isset($_GET['année'])){
            $mois = $_GET['mois'];
            $annee = $_GET['année'];
        }

        $date_test = mktime( 0, 0, 0, $mois, 1, $annee );
        $fin_mois = date('t', $date_test) + 1;


        $date_Debut = \ DateTime::createFromFormat('d/m/Y h:i', '1/'.$mois.'/'.$annee.' 00:00');
        $date_Fin = \ DateTime::createFromFormat('d/m/Y', $fin_mois.'/'.$mois.'/'.$annee);


        $dateTestEmploye =  \ DateTime::createFromFormat('d/m/Y h:i', ($fin_mois-1).'/'.$mois.'/'.$annee.' 00:00');

        $dateDebut = $date_Debut->format('Y-m-d');
        $dateFin = $date_Fin->format('Y-m-d');

        $repository_employe = $em->getRepository('EmployeBundle:Employe');
        $repository_historiquePaie = $em->getRepository('PaieBundle:Historique_Paie');

        $emps = $repository_employe->findBy(array(),array('matricul' => 'asc'));

        $employes = array();

        // ------------------- MILA ANNDRAMANA IO AMBANY IO !!!!!!!!!!!!!!!!!!!!!!!!!!!! ---------------------

        foreach ($emps as $emp){
            if($emp->getEtat() and $emp->getDateDebut() < $dateTestEmploye){
                array_push($employes, $emp);
            }elseif (($emp->getDateDebauche() >= $date_Debut and $emp->getDateDebauche()) )
                array_push($employes, $emp);


        }

        // ------------------- ////// MILA ANNDRAMANA IO AMBANY IO !!!!!!!!!!!!!!!!!!!!!!!!!!!! ////// ---------------------



        $listes = array();

        foreach ($employes as $employe){
//            $employe = new Employe(); // --------  A COMMENTER ----------

            $entite = array();
            $entite['matricule'] = $employe->getMatricul();
            $entite['nom'] = $employe->getNom();
            $entite['prenom'] = $employe->getPrenom();
            $entite['poste'] = $employe->getPoste();
            $entite['nbEnfant'] = $employe->getNbEnfant();
            $entite['empSalaire'] = $employe->getSalaire();
            $entite['numeroCin'] = $employe->getNumCin();
            $entite['numeroCnaps'] = $employe->getCnaps();
            $entite['employeSlug'] = $employe->getSlug();
            $entite['paiement']= $employe->getMethodePaiement();

            $entite['numero'] = $employe->getNumeroCompte();
            $entite['banque'] = $employe->getNomBanque();
            $entite['beneficiaire'] = $employe->getBeneficiaire();


            $compte_Paie = $employe->getComptePaie();

            $historique_paies = $repository_historiquePaie->findByMoisAnne($compte_Paie->getSlug(), $dateDebut, $dateFin);

            $salaireBase = null;
            $allocConge = null;
            $heureSuppExonerer = null;
            $heureSuppDeductible = null;
            $avanceQz = 0;
            $avanceSpecial = 0;
            $ostie = null;
            $cnaps = null;
            $irsa = null;
            $autreDeductions = array();
            $autreCredits = array();
            $tmpNbEnfant = null;
            $tmpSalaireBase = null;
            $tmpMinimumPerception = null;
            $jourTravailler = null;



            foreach ($historique_paies as $historique_paie){
//            $historique_paie = new Historique_Paie(); // ---------------- A COMMENTER --------------

                $historique_speciale = $em->getRepository('PaieBundle:Historique_AvanceSpecial')->findOneBy(array('historique_paie'=>$historique_paie));

                // -------------------------------------------

                if($historique_paie->getSalaireBase())
                    $salaireBase = $historique_paie;

                if($historique_paie->getType() == "base_final")
                    $jourTravailler = $historique_paie->getNbJour();

                if ($historique_paie->getType() == "alloc_final"){
                    $allocConge = $historique_paie;
                    $jourTravailler += $allocConge->getNbJour();
                }


                if ($historique_paie->getType() == "exonerer")
                    $heureSuppExonerer = $historique_paie;

                if ($historique_paie->getType() == "deductible")
                    $heureSuppDeductible = $historique_paie;

                if($historique_paie->getType() == "tmp_base"){
                    $tmpSalaireBase = $historique_paie;
                }

                if($historique_paie->getType() == "tmp_nbEnfant"){
                    $tmpNbEnfant = $historique_paie;
                }

                if($historique_paie->getType() == "tmp_minimumPerception"){
                    $tmpMinimumPerception = $historique_paie;
                }

                if ($historique_paie->getType() == "debit"){
                    if(strpos($historique_paie->getLibelle(), 'Avance sur salaire') !== false){
                        $avanceQz += $historique_paie->getMontant();
                    }
                    elseif(strpos($historique_paie->getLibelle(), 'Remboursement Avance') !== false or $historique_speciale){
                        $avanceSpecial += $historique_paie->getMontant();
                    }
                    elseif($historique_paie->getOstie()){
                        $ostie = $historique_paie;
                    }
                    elseif($historique_paie->getCnaps()){
                        $cnaps = $historique_paie;
                    }
                    elseif($historique_paie->getIrsa()){
                        $irsa = $historique_paie;
                    }else {
                        if ($historique_paie->getType() == 'debit' and !$historique_paie->getPriseSalaire()) {
                            array_push($autreDeductions, $historique_paie);
                        }
                    }
                }

                if ($historique_paie->getType() == "credit" and ! $historique_paie->getSalaireBase())
                    array_push($autreCredits, $historique_paie)
                    ;
            }

            $totalCredit = $salaireBase->getMontant();

            if($allocConge)
                $totalCredit += $allocConge->getMontant();

            if($heureSuppExonerer)
                $totalCredit += $heureSuppExonerer->getMontant();

            if($heureSuppDeductible)
                $totalCredit += $heureSuppDeductible->getMontant();

            foreach ($autreCredits as $autreCredit){
                $totalCredit += $autreCredit->getMontant();
            }

            $totalDebit = 0;
            foreach ($autreDeductions as $autreDeduction){
                $totalDebit += $autreDeduction->getMontant();
            }
            if($ostie)
                $totalDebit += $ostie->getMontant();

            if($cnaps)
                $totalDebit += $cnaps->getMontant();

            if($irsa)
                $totalDebit += $irsa->getMontant();

            $totalDebit = $totalDebit + $avanceQz + $avanceSpecial;

            // ------------ MIOVA IZY ETO -------------
            // ------------ MIOVA IZY ETO -------------
            // ------------ MIOVA IZY ETO -------------
            // ------------ MIOVA IZY ETO -------------
            // ------------ MIOVA IZY ETO -------------

            $tmpAvantage = 0;
            foreach ($autreCredits as $autreCredit){
                $tmpAvantage += $autreCredit->getMontant();
            }

            $brutDuMois = $salaireBase->getMontant();
            if($allocConge)
                $brutDuMois += $allocConge->getMontant();

            if($heureSuppExonerer)
                $brutDuMois += $heureSuppExonerer->getMontant();

            if($heureSuppDeductible)
                $brutDuMois += $heureSuppDeductible->getMontant();

            foreach ($autreCredits as $autreCredit){
                $brutDuMois += $autreCredit->getMontant();
            }

            // ---- CALCUL CNAPS 13 % & 5% OSTIE ----

            $cnaps13 = 0;
            if ($cnaps){
                $cnaps13 = (0.13 * $cnaps->getMontant()) / 0.01;
            }

            $ostie5 = 0;
            if ($ostie){
                $ostie5 = (0.05 * $ostie->getMontant()) / 0.01;
            }

            // --------  FMPFP   --------
            $fmfp = $brutDuMois * 0.01;
            if($fmfp > 16000){
                $fmfp = 16000;
            }

            // ---- RETENU AVANCE -----
            $totalAvance = $avanceSpecial + $avanceQz;


            // --- AUTRE AUTRE -----
            $totalAutre = 0;
            if ($heureSuppExonerer){
                $totalAutre += $heureSuppExonerer->getMontant();
            }

            if ($autreDeductions){
                foreach ($autreDeductions as $autreDeduction){
                    $totalAutre -= $autreDeduction->getMontant();
                }
            }


            $entite['mois'] = $mois;
            $entite['annee'] = $annee;
            $entite['date_Debut'] = $date_Debut;
            $entite['date_Fin'] = $date_Fin;
            $entite['salaireBase'] = $salaireBase;
            $entite['allocConge'] = $allocConge;
            $entite['heureSuppExonerer'] = $heureSuppExonerer;
            $entite['heureSuppDeductible'] = $heureSuppDeductible;
            $entite['avanceQz'] = $avanceQz;
            $entite['avanceSp'] = $avanceSpecial;
            $entite['ostie'] = $ostie;
            $entite['cnaps'] = $cnaps;
            $entite['irsa'] = $irsa;
            $entite['autreDeduction'] = $autreDeductions;
            $entite['autreCredits'] = $autreCredits;
            $entite['totalDebit'] = $totalDebit;
            $entite['totalCredit'] = $totalCredit;
            $entite['tmpSalaireBase'] = $tmpSalaireBase;
            $entite['tmpNbEnfant'] = $tmpNbEnfant;
            $entite['tmpMinimumPerception'] = $tmpMinimumPerception;

            //------------ FANAMINY  --------------
            $entite['totalAvantage'] = $tmpAvantage;
            $entite['brutDuMois'] = $brutDuMois;
            $entite['cnaps13'] = $cnaps13;
            $entite['ostie5'] = $ostie5;
            $entite['fmfp'] = $fmfp;
            $entite['totalAvance'] = $totalAvance;
            $entite['totalAutre'] = $totalAutre;
            $entite['jourTravailler'] = $jourTravailler;


            //-------------- FIN ------------------
            array_push($listes, $entite);

        }

        $societe = $em->getRepository('EmployeBundle:Societe')->findOneBy(array(
            'societeMere' => true
        ));

        if(isset($_GET['type']))
        {
            if($_GET['type'] == "avances")
            {

                $repository_demandeAvance = $em->getRepository('PaieBundle:Demande_Avance');

               /* foreach ($listes as $entite){
                    $dmdAvcs= $repository_demandeAvance->findByMoisAnne($entite['employeSlug'],$dateDebut,$dateFin);

                    $avanceQz = 0;

                    if($dmdAvcs){
                        foreach ($dmdAvcs as $dmdAvc){

                            if($dmdAvc->getEtat()=='En attente de comptabilisation' or $dmdAvc->getEtat()=='Demande acceptée' )
                            {

                                $avanceQz+=$dmdAvc->getMontant();


                            }
                        }

                    }
                    $entite['avanceQz'] = $avanceQz;


                }*/

                $tmpListe = array();

                $netTotal=0;

                foreach ($listes as $entite){

                    $dmdAvcs= $repository_demandeAvance->findByMoisAnne($entite['employeSlug'],$dateDebut,$dateFin);

                    $avanceQz = 0;

                    if($dmdAvcs){
                        foreach ($dmdAvcs as $dmdAvc){

                            if($dmdAvc->getEtat()=='En attente de comptabilisation' or $dmdAvc->getEtat()=='Demande acceptée' )
                            {

                                $avanceQz+=$dmdAvc->getMontant();


                            }
                        }

                    }

                   if( $avanceQz > 0 or $entite['avanceSp'] > 0){
                       $entite['avanceQz'] = $avanceQz;
                       $netTotal= $netTotal+$avanceQz;
                       array_push($tmpListe, $entite);
                   }

                }

                $chiffreEnLettre = new ChiffreEnLettreService();

                $netEnLettre = $chiffreEnLettre->Conversion($netTotal);

                return $this->render('@Paie/paie/etat_avances.html.twig', array(
                    'listes' => $tmpListe,
                    'mois' => $mois,
                    'annee' => $annee,
                    'societe' => $societe,
                    'debut'=>$date_Debut,
                    'fin' =>$date_Fin,
                    'total'=>$netEnLettre,


                ));
            }
        }

        if(isset($_GET['type'])){

            if($_GET['type'] == "irsa"){

                $varNif = str_split(str_replace(' ', '', $societe->getNif()));

                $varStat = str_split(str_replace(' ', '', $societe->getStat()));

                return $this->render('@Paie/paie/etat_irsa.html.twig', array(
                    'listes' => $listes,
                    'mois' => $mois,
                    'annee' => $annee,
                    'societe' => $societe,
                    'varNif' => $varNif,
                    'varStat' => $varStat,

                ));
            }
        }
        if(isset($_GET['type'])){

            if($_GET['type'] == "virement"){

                $tmpListes = array();
                $totalNet =0;

                foreach ($listes as $entite) {

                    if ($entite['paiement']=='Virement') {
                        $net =$entite['totalCredit']-$entite['totalDebit'];
                        $totalNet+=round($net);
                        array_push($tmpListes, $entite);
                    }
                }

                    $chiffreEnLettre = new ChiffreEnLettreService();

                    $netEnLettre = $chiffreEnLettre->Conversion($totalNet);

                    return $this->render('@Paie/paie/ordreVirement.html.twig', array(
                    'listes' => $tmpListes,
                    'mois' => $mois,
                    'annee' => $annee,
                    'societe' => $societe,
                    'totalLettre'=>$netEnLettre


                ));
            }
        }

        return $this->render('@Paie/paie/etat_salire.html.twig', array(
            'listes' => $listes,
            'mois' => $mois,
            'annee' => $annee,
            'societe' => $societe
        ));

    }


}


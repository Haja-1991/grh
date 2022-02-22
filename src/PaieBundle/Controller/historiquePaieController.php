<?php

namespace PaieBundle\Controller;

use HistoriqueGlobalBundle\Entity\Historique_Global;
use PaieBundle\PaieBundle;
use PaieBundle\Services\PaieService;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;

use PaieBundle\Entity\Compte_Paie;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

use EmployeBundle\Entity\Employe;

use PaieBundle\Entity\Historique_Paie;

/**
 * historiquePaie controller.
 *
 * @Route("/")
 */
class historiquePaieController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('', array('name' => $name));
    }
    /**
     * Lists all Employe entities.
     *
     * @Route("/debit/{slug}", name="debit_paie")
     * @Method("POST")
     */
    public function ajouterDebitAction(Compte_Paie $compte_Paie)
    {

        $em = $this->getDoctrine()->getManager();

        $historique_paie=new Historique_Paie();

        $historique_paie->setComptePaie($compte_Paie);

        $request = $this->get('request');

        $date = \DateTime::createFromFormat('d/m/Y', $_POST['date_debit']);

        $date1 = $date->format('Y-m-d H:i:s');

        if($request->getMethod() == 'POST'){

            $historique_paie->setDate(new \DateTime($date1));

            $historique_paie->setLibelle($_POST['libelle']);

            $historique_paie->setMontant($_POST['Somme_debit']);

            $historique_paie->setType('debit');

            $em->persist($historique_paie);

            $em->flush();

            return $this->redirect($this->generateUrl('paie_detail',

                array('slug' => $compte_Paie->getSlug())));

        }
        return new Response('Erreur 404');



    }
    /**
     * Lists all Employe entities.
     *
     * @Route("/credit/{slug}", name="credit_paie")
     * @Method("POST")
     */
    public function ajouterCreditAction(Compte_Paie $compte_Paie)
    {

        $em = $this->getDoctrine()->getManager();

        $historique_paie=new Historique_Paie();

        $historique_paie->setComptePaie($compte_Paie);

        $request = $this->get('request');

        $date = \DateTime::createFromFormat('d/m/Y', $_POST['date_credit']);

        $date1 = $date->format('Y-m-d H:i:s');

        if($request->getMethod() == 'POST'){

            $historique_paie->setDate(new \DateTime($date1));

            $historique_paie->setLibelle($_POST['libelle']);

            $historique_paie->setMontant($_POST['Somme_debit']);

            $historique_paie->setType('credit');

            $em->persist($historique_paie);

            $em->flush();

            return $this->redirect($this->generateUrl('paie_detail',

                array('slug' => $compte_Paie->getSlug())));

        }
        return new Response('Erreur 404');
    } /**

    /**
     * historiquepaie entities.
     *
     * @Route("/modifier/{id}", name="modif_histo_paie")
     * @Method({"GET", "POST"})
     */
    public function modifHistoPaieAction(Historique_Paie $historique_Paie)
    {
        $em=$this->getDoctrine()->getManager();

        $request = $this->get('request');

        $test_modification = array(
            'date' => $historique_Paie->getDate()->format('d/m/Y'),
            'montant' => $historique_Paie->getMontant(),
            'libelle' => $historique_Paie->getLibelle()
        );

        $historique_Paie->setTestModification($test_modification);

        $em->persist($historique_Paie);
        $em->flush();

        if($request->getMethod() == 'POST'){

            $date1 = \DateTime::createFromFormat('d/m/Y', $_POST['date_hp']);
            $historique_Paie->setDate($date1);
            $historique_Paie->setMontant($_POST['montant']);
            $historique_Paie->setLibelle($_POST['libelle']);

            $historique_special=$em->getRepository('PaieBundle:Historique_AvanceSpecial')->findOneBy(array('historique_paie'=>$historique_Paie));
            if ($historique_special)
                {

                    $historique_special->setDate($date1);
                    $historique_special->setMontant($historique_Paie->getMontant());
                    $historique_special->setLibelle($historique_Paie->getLibelle());
                    $em->persist($historique_special);

                    $historique_specialUpdates = $em->getRepository('PaieBundle:Historique_AvanceSpecial')->findBy(array('avanceSpecial'=>$historique_special->getAvanceSpecial()));

                    $somme=0;

                    foreach ( $historique_specialUpdates as $valeur)
                    {
                        $somme = $somme + floatval($valeur->getMontant());

                    }
                    $historique_special->getAvanceSpecial()->setSommePaier($somme);
                    $em->persist($historique_special);

                }


            $em->flush();

            $my_test = $historique_Paie->getTestModification();

            $valueTest = array();

            if($my_test['date'] != $historique_Paie->getDate()->format('d/m/Y')){
                array_push($valueTest, array(
                    'libelle' => 'Date',
                    'ancien' => $my_test['date'],
                    'nouveau' => $historique_Paie->getDate()->format('d/m/Y')
                ));
            }

            if($my_test['montant'] != $historique_Paie->getMontant()){
                array_push($valueTest, array(
                    'libelle' => 'Montant',
                    'ancien' => $my_test['montant'],
                    'nouveau' => $historique_Paie->getMontant()
                ));
            }

            if($my_test['libelle'] != $historique_Paie->getLibelle()){
                array_push($valueTest, array(
                    'libelle' => 'Libellé',
                    'ancien' => $my_test['libelle'],
                    'nouveau' => $historique_Paie->getLibelle()
                ));
            }

            if(count($valueTest) > 0){
                $historiqueGlobal = new Historique_Global();

                $employe = $em->getRepository('EmployeBundle:Employe')->findOneBy(array('comptePaie' => $historique_Paie->getComptePaie()));
                $historiqueGlobal->setLibelle('Modification d\'une valeur dans le compte de '.$employe->getNomComplet());
                $historiqueGlobal->setDate(new \DateTime());
                $historiqueGlobal->setLien($this->generateUrl('paie_detail', array('slug' => $historique_Paie->getComptePaie()->getSlug())));
                $historiqueGlobal->setUser($this->getUser());
                $historiqueGlobal->setModification($valueTest);

                $em->persist($historiqueGlobal);
            }


            $em->persist($historique_Paie);
            $em->flush();

            return $this->redirect($this->generateUrl('paie_detail', array('slug' => $historique_Paie->getComptePaie()->getSlug())));

        }
        return $this->render('PaieBundle:paie:modifHistopaie.html.twig',array('histo_paie'=>$historique_Paie));
    }
    /**
     * historique_paie Employe entities.
     *
     * @Route("/delete_histo_paie/{id}", name="delete_histo_paie")
     * @Method("GET")
     */
    public function deleteHistoPaieAction(Historique_Paie $historique_Paie){


        $em=$this->getDoctrine()->getManager();

        if($historique_Paie->getDemandeAvance())
        {
            $em->remove($historique_Paie->getDemandeAvance());
            $em->remove($historique_Paie);
        }

        if($historique_Paie->getDemandePaimenentConge())
        {
            $em->remove($historique_Paie->getDemandePaimenentConge());
            $em->remove($historique_Paie);
        }

        $historique_special= $em->getRepository('PaieBundle:Historique_AvanceSpecial')->findOneBy(array('historique_paie'=>$historique_Paie));
        if($historique_special)
        {
            $historique_specialUpdate = $em->getRepository('PaieBundle:Historique_AvanceSpecial')->findBy(array('avanceSpecial'=>$historique_special->getAvanceSpecial()));
            $somme=0;

            foreach ( $historique_specialUpdate as $valeur)
            {
                $somme = $somme + floatval($valeur->getMontant());
            }

            $historique_special->getAvanceSpecial()->setSommePaier($somme);
            $em->persist($historique_special);
            $em->remove($historique_special);
        }

        $historique_abs=$em->getRepository('PresenceBundle:Historique_Absence')->findOneBy(array('historique_paie_abs'=>$historique_Paie));
        if($historique_abs){
            $historique_abs->setHistoriquePaieAbs(null);
            $em->persist($historique_abs);
        }

        $em->remove($historique_Paie);


        $em->flush();
        return $this->redirect($this->generateUrl('paie_detail', array('slug' => $historique_Paie->getComptePaie()->getSlug())));
    }

    /**
     * COMPTABILISER RESTE SALAIRE
     *
     * @Route("/comptabiliser/reste/{slug}", name="paie_comptabiliser_salaire")
     * @Method("POST")
     */
    public function comptabiliserSalaire(Compte_Paie $compte_Paie){

        $request = $this->get('request');
        $em = $this->getDoctrine()->getManager();

        if($request->getMethod() == 'POST'){
            $histo_paie=new Historique_Paie();
            $histo_paie->setDate( new\DateTime());

            $mois = $_POST['mois'];
            $annee = $_POST['annee'];

            if($histo_paie->getDate()->format('m') != $mois){

                $date_test = mktime( 0, 0, 0, $mois, 1, $annee );
                $fin_mois = date('t', $date_test);

                $dateFin = \ DateTime::createFromFormat('d/m/Y', $fin_mois.'/'.$mois.'/'.$annee);

                $histo_paie->setDate($dateFin);

            }

            $histo_paie->setLibelle("Reste salaire");
            $histo_paie->setType("debit");
            $histo_paie->setComptePaie($compte_Paie);
            $histo_paie->setMontant($_POST['resteSalaire']);
            $histo_paie->setPriseSalaire(true);
            $histo_paie->setCaisse($_POST['caisse_debiter']);

            $em->persist($histo_paie);

            $em->flush();

            return $this->redirect($this->generateUrl('paie_detail',array(
                'slug' => $compte_Paie->getSlug(),
                'mois' => $mois,
                'année' => $annee
            )));

        }

    }

    /**
     * COMPTABILISER RESTE SALAIRE
     *
     * @Route("/deduction/salaire/{slug}", name="paie_deduction_salaire")
     * @Method("POST")
     */
    public function deductionSalaireAction(Compte_Paie $compte_Paie){

        $request = $this->get('request');

        if($request->getMethod() == 'POST'){
            $em = $this->getDoctrine()->getManager();
            $dateActuel = new \DateTime();

            $dateFin = $dateActuel;

            $employe = $em->getRepository('EmployeBundle:Employe')->findOneBy(
                array('comptePaie' => $compte_Paie)
            );

            $mois = $_POST['mois'];
            $annee = $_POST['annee'];


            $date_test = mktime( 0, 0, 0, $mois, 1, $annee );
            $fin_mois = date('t', $date_test);
            $dateFin = \ DateTime::createFromFormat('d/m/Y', $fin_mois.'/'.$mois.'/'.$annee);


            $dateDebut = \ DateTime::createFromFormat('d/m/Y', '1/'.$mois.'/'.$annee);

            $paieService = new PaieService();
            $paieService->deductionSalaireEmploye($em, $compte_Paie, $employe, $dateDebut, $dateFin);

            $em->flush();

            return $this->redirect($this->generateUrl('paie_detail',array(
                'slug' => $compte_Paie->getSlug(),
                'mois' => $mois,
                'année' => $annee
            )));
        }

    }
    /**
     * COMPTABILISER RESTE SALAIRE
     *
     * @Route("/annuler/deduction/salaire/{slug}", name="annuler_paie_deduction_salaire")
     * @Method("POST")
     */
    public function Annuler_deductionSalaireAction(Compte_Paie $compte_Paie){

        $request = $this->get('request');

        if($request->getMethod() == 'POST'){
            $em = $this->getDoctrine()->getManager();
            $dateActuel = new \DateTime();

            $dateFin = $dateActuel;

            $employe = $em->getRepository('EmployeBundle:Employe')->findOneBy(
                array('comptePaie' => $compte_Paie)
            );

            $mois = $_POST['mois'];
            $annee = $_POST['annee'];


            $date_test = mktime( 0, 0, 0, $mois, 1, $annee );
            $fin_mois = date('t', $date_test);

            $dateFin = \ DateTime::createFromFormat('d/m/Y', $fin_mois.'/'.$mois.'/'.$annee);


            $dateDebut = \ DateTime::createFromFormat('d/m/Y', '1/'.$mois.'/'.$annee);

            $respositoryHistoriquePaie = $em->getRepository('PaieBundle:Historique_Paie');
            $dateDebut = \DateTime::createFromFormat('d/m/Y h:i', ''.$dateDebut->format('d').'/'.$dateDebut->format('m').'/'.$dateDebut->format('Y').' '."00:00");
            $historiques = $respositoryHistoriquePaie->findByMoisAnne($compte_Paie->getSlug(), $dateDebut, $dateFin);

           foreach($historiques as $historique){
               if($historique->getOstie() or $historique->getCnaps() or
                   $historique->getIrsa() or $historique->getType()=='alloc_final' or
                   $historique->getType()=='exonerer' or  $historique->getType()=='deductible' or
                   $historique->getType()=='tmp_reste_conge'
                   or  $historique->getType()=='tmp_minimumPerception' or  $historique->getType()=='tmp_nbEnfant'
                   or  $historique->getType()=='tmp_base'){

                   $em->remove($historique);
               }

               if($historique->getType()=='verification' ){
                   $verification=$historique->getVerification();
                   $em->remove($verification);
                  $em->remove($historique);
               }
               if($historique->getSalaireBase() or  $historique->getType()=='base_finale' ){
                   $historique->setType('credit');
                   $historique->setSalaireBase(true);
                  // $em->remove($historique);
               }
           }
          // return new Response(var_dump($historiques));

            $em->flush();

            return $this->redirect($this->generateUrl('paie_detail',array(
                'slug' => $compte_Paie->getSlug(),
                'mois' => $mois,
                'année' => $annee
            )));
        }

    }

    /**
     * COMPTABILISER RESTE SALAIRE
     *
     * @Route("/nouveau/conge/{id}", name="new_reste_conge")
     * @Method("POST")
     */
    public function ModifierResteCongeAction(Historique_Paie $historique)
    {

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
            $em = $this->getDoctrine()->getManager();
            $compte_Paie=$historique->getComptePaie();
            $employe = $em->getRepository('EmployeBundle:Employe')->findOneBy(
                array('comptePaie' => $compte_Paie)
            );
            //$historique=$em->getRepository('PaieBundle:Historique_Paie')->findOneBy(array("comptePaie" => $compte_Paie,"type" =>'tmp_reste_conge'));
            $historique->setMontant($_POST['conge']);
            //$employe->getComptePresence()->setResteConge($_POST['conge']);
            $em->flush();
            return $this->redirect($this->generateUrl('paie_detail',
                array('slug' => $compte_Paie->getSlug()))
            );

        }
    }
    /**
     * HEURE SUPP.
     *
     * @Route("/heure-supp/{slug}", name="paie_heure_supp")
     * @Method("POST")
     */
    public function heureSuppAction(Compte_Paie $compte_Paie){
        $em = $this->getDoctrine()->getManager();

        $historique_paie=new Historique_Paie();

        $historique_paie->setComptePaie($compte_Paie);

        $request = $this->get('request');

        $date = \DateTime::createFromFormat('d/m/Y', $_POST['date_heureSup']);

        if($request->getMethod() == 'POST'){

            $historique_paie->setDate($date);

            $historique_paie->setLibelle($_POST['libelle']);

            $historique_paie->setNbHeure($_POST['nb_heureSup']);
            $historique_paie->setMajoration($_POST['majoration']);

            $employe = $em->getRepository('EmployeBundle:Employe')->findOneBy(array("comptePaie" => $compte_Paie));
            $salaire_pH = $employe->getSalaire() / 30 / 8;

            $montant = ($salaire_pH * $historique_paie->getNbHeure()) + ($salaire_pH * $historique_paie->getNbHeure() * $historique_paie->getMajoration() / 100);

            $historique_paie->setMontant($montant);

            $historique_paie->setType('heureSup');

            $em->persist($historique_paie);

            $em->flush();

            return $this->redirect($this->generateUrl('paie_detail',
                array('slug' => $compte_Paie->getSlug()))
            );

        }
        return new Response('Erreur 404');

    }

    /**
     * COMPTABILISER RESTE SALAIRE
     *
     * @Route("/deduction/virement/salaire/{slug}", name="paie_reste_virement")
     * @Method("POST")
     */
    public function resteParVirementAction(Compte_Paie $compte_Paie){
        $em = $this->getDoctrine()->getManager();

        $histo_paie=new Historique_Paie();
        $histo_paie->setDate( new\DateTime());

        $mois = $_POST['mois'];
        $annee = $_POST['annee'];

        if($histo_paie->getDate()->format('m') != $mois){
            $date_test = mktime( 0, 0, 0, $mois, 1, $annee );
            $fin_mois = date('t', $date_test);

            $dateFin = \ DateTime::createFromFormat('d/m/Y', $fin_mois.'/'.$mois.'/'.$annee);

            $histo_paie->setDate($dateFin);
        }

        $histo_paie->setLibelle("Reste salaire");
        $histo_paie->setType("debit");
        $histo_paie->setComptePaie($compte_Paie);
        $histo_paie->setMontant($_POST['resteSalaire']);
        $histo_paie->setPriseSalaire(true);
        $histo_paie->setCaisse('Virement Banquaire');

        $em->persist($histo_paie);

        $em->flush();

        return $this->redirect($this->generateUrl('paie_detail',array(
            'slug' => $compte_Paie->getSlug(),
            'mois' => $mois,
            'année' => $annee
        )));

    }




}

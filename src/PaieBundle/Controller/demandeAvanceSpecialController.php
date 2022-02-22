<?php

namespace PaieBundle\Controller;

use HistoriqueGlobalBundle\Entity\Historique_Global;
use PaieBundle\Entity\Compte_Paie;
use PaieBundle\Entity\Demande_Avance;
use PaieBundle\Entity\Historique_AvanceSpecial;
use PaieBundle\Entity\Numero_demande;
use PaieBundle\PaieBundle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use EmployeBundle\Entity\Employe;
use PaieBundle\Entity\Avance_Special;
use PaieBundle\Entity\Historique_Paie;

/**
 * demandeAvanceSpecial controller.
 *
 * @Route("/avance-spécial")
 */

class demandeAvanceSpecialController extends Controller
{

    public function recupererNumero($em){

        $repository_numDemande = $em

            ->getRepository('PaieBundle:Numero_demande');

        $numRetour = '';

        $qNumDemande = $repository_numDemande->findOneBy(array('nom_config'=>'numero_demande_avance_special'));

        $numDemande = $qNumDemande;

        $dateJour  = new \DateTime();

        $numRetour = 'N° AS-'.$numDemande->getNumeroDemande().'/'.$dateJour->format('y');

        return $numRetour;

    }

    /**
     * Avance_Special entities.
     *
     * @Route("/demande", name="faire_demande_avancespeciale")
     * @Method({"GET", "POST"})
     */
    public function nouvelleDemandeAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repository_employe = $em->getRepository('EmployeBundle:Employe') ;
        $num=$this->recupererNumero($em);

        $employes = $repository_employe->findBy(
            array('etat' => true),
            array('nomComplet' => 'ASC')
        );

        $userActif = $this->getUser();

        if (!$userActif->ifRole('ROLE_PAIE')){
            $employes = $repository_employe->findBy(
                array('etat' => true, 'userCompte' => $userActif),
                array('nomComplet' => 'ASC')
            );
        }

        if($userActif->ifRole('ROLE_SEC')){
            $employes = $repository_employe->findBy(
                array('etat' => true),
                array('nomComplet' => 'ASC')
            );
        }

        $request = $this->get('request');
        $usercreer=$this->getUser();

        if($request->getMethod() == 'POST'){
            $repository_numDemande = $em

                ->getRepository('PaieBundle:Numero_demande');

            $numDemande = $repository_numDemande->findOneBy(array('nom_config'=>'numero_demande_avance_special'));
            
            $intNextNum = intval($numDemande->getNumeroDemande()) + 1;

            $nextNum = $intNextNum;

            if(strlen($intNextNum) == 1){
                $nextNum = '000'.$intNextNum;
            }

            if(strlen($intNextNum) == 2){
                $nextNum = '00'.$intNextNum;
            }

            if(strlen($intNextNum) == 3){
                $nextNum = '0'.$intNextNum;
            }

            $numDemande->setNumeroDemande($nextNum);


            $employe = $em->getRepository('EmployeBundle:Employe')->findOneBy(array('nomComplet'=>$_POST['employe']));

            $date = new \DateTime();

            $demande_avance = new Avance_Special();
            $demande_avance->setUserCreer($usercreer);
            $demande_avance->setEmpoye($employe);
            $demande_avance->setDate($date);
            $demande_avance->setMontant($_POST['montant']);
            $demande_avance->setParMois($_POST['montantParMois']);
            $demande_avance->setDescription($_POST['desc']);
            $demande_avance->setSommePaier(0);
            $demande_avance->setEtat(false);
            $demande_avance->setEtatDemande('En attente de confirmation');

            $demande_avance->setNumero($_POST['numero']);
            $demande_avance->setDemander(true);

            $em->persist($demande_avance);
            $em->persist($numDemande);
            $em->flush();

            //            ---HISTORIQUE GLOBAL-----

            $historiqueGlobal = new Historique_Global();

            $historiqueGlobal->setLibelle('Création d\'une nouvelle demande d\'avance '.$demande_avance->getNumero());
            $historiqueGlobal->setDate(new \DateTime());
            $historiqueGlobal->setLien($this->generateUrl('formulaire_confirme', array('id' => $demande_avance->getId())));
            $historiqueGlobal->setUser($userActif);

            $em->persist($historiqueGlobal);

            //            ---HISTORIQUE GLOBAL-----

            $em->flush();

//            return $this->redirect($this->generateUrl('formulaire_confirme', array('id' => $demande_avance->getId())));
            return $this->redirectToRoute('avanceSpeciel_detail', array('id' => $demande_avance->getId()));
        }
        return $this->render('@Paie/avanceSpecial/nouvelle_demande.html.twig', array(
            'employes' => $employes,
            'num'=>$num
        ));
    }


    /**
     * Avance_Special entities.
     *
     * @Route("/inscrire", name="ajouter_avancespeciale")
     * @Method({"GET", "POST"})
     */
    public function ajouterAvanceSpecialAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repository_employe = $em->getRepository('EmployeBundle:Employe') ;
        $employes = $repository_employe->findBy(
            array('etat' => true),
            array('nomComplet' => 'ASC')
        );
        $request = $this->get('request');

        $avance_special=new Avance_Special();

        $usercreer=$this->getUser();

        if($request->getMethod() == 'POST'){

            $employe = $em->getRepository('EmployeBundle:Employe')->findOneBy(array('nomComplet'=>$_POST['employe']));

            $date = new \DateTime();

            $avance_special->setUserCreer($usercreer);

            $avance_special->setEmpoye($employe);

            $avance_special->setDate($date);

            $avance_special->setMontant($_POST['montant']);

            $avance_special->setParMois($_POST['montantParMois']);

            $avance_special->setSommePaier(0);

            $avance_special->setEtat(true);

            if(isset($_POST['mois_inclu'])){

                $avance_special->setSommePaier(($avance_special->getSommePaier() + $avance_special->getParMois()));

                $histo_paie=new Historique_Paie();

                $histo_paie->setComptePaie($employe->getComptePaie());

                $histo_paie->setMontant($avance_special->getParMois());

                $histo_paie->setType("debit");

                $histo_paie->setLibelle("Remboursement Avance special");

                $histo_paie->setButtonModif(true);

                $histo_paie->setDate($date);

                $em->persist($histo_paie);

                $histo_av = new Historique_AvanceSpecial();
                $histo_av->setHistoriquePaie($histo_paie);
                $histo_av->setMontant($avance_special->getParMois());
                $histo_av->setDate($histo_paie->getDate());

                $histo_av->setAvanceSpecial($avance_special);

                $em->persist($histo_av);

            }

            $em->persist($avance_special);


            //            ---HISTORIQUE GLOBAL-----

            $historiqueGlobal = new Historique_Global();

            $historiqueGlobal->setLibelle('Création d\'une avannce spécial pour: '.$employe->getNomComplet());
            $historiqueGlobal->setDate(new \DateTime());
            $historiqueGlobal->setLien('#');
            $historiqueGlobal->setUser($this->getUser());

            $em->persist($historiqueGlobal);

            $em->flush();
            //            ---HISTORIQUE GLOBAL-----

            return $this->redirect($this->generateUrl('listeAvanceSpeciale'));

        }
        return $this->render('@Paie/avanceSpecial/new.html.twig', array(
            'employes' => $employes,
        ));




    }
    /**
     * Avance_Special entities.
     *
     * @Route("/", name="listeAvanceSpeciale")
     * @Method("GET")
     */
    public function AfficherAvanceSpecialAction()
    {
        if(!$this->getUser()->ifRole('ROLE_PAIE')){
            return new Response('404 NOT-FOUND');
        }

        $em=$this->getDoctrine()->getManager();

        $avanceSpecials = $em->getRepository('PaieBundle:Avance_Special')
            ->createQueryBuilder('avanceSpecial')
            ->where('avanceSpecial.demander = 0')
            ->orWhere('avanceSpecial.demander is null')
            ->getQuery()->getResult()
        ;

        $demandeAvanceSpecials = $em->getRepository('PaieBundle:Avance_Special')
            ->createQueryBuilder('avanceSpecial')
            ->where('avanceSpecial.demander = 1')
            ->getQuery()->getResult()
        ;

        return $this->render('@Paie/avanceSpecial/index.html.twig', array(
            'avanceSpecials' => $avanceSpecials,
            'demandeAvanceSpecials' => $demandeAvanceSpecials,
            'date' =>"asc"
        ));
    }

    /**
     * Avance_Special entities.
     *
     * @Route("/KDAL{id}KMDA/UI5DAK215", name="avanceSpeciel_detail")
     * @Method("GET")
     */
    public function detailAvanceSpecialAction(Avance_Special $avance_Special)
    {
        $em = $this->getDoctrine()->getManager();
        $employe = $avance_Special->getEmpoye();

        $repository_historique = $em->getRepository('PaieBundle:Historique_AvanceSpecial');

        $historiques = $repository_historique->findBy(
            array('avanceSpecial' => $avance_Special),
            array("date" => "asc")
        );

        $servIP = $this->getRequest()->server->get('SERVER_ADDR');
        if ($avance_Special->getDemander()){
            return $this->render('@Paie/avanceSpecial/info_nouvelle_demande.html.twig',array(
                'avanceSpecial' => $avance_Special,
                'employe' => $employe,
                'historiques' => $historiques,
                'servIP' => $servIP
            ));
        }

        return $this->render('@Paie/avanceSpecial/show.html.twig',array(
            'avanceSpecial' => $avance_Special,
            'employe' => $employe,
            'historiques' => $historiques, 
        ));
    }


    /**
     * Avance_Special entities.
     *
     * @Route("/46546{id}454566/modifier", name="avancespeciale_modifier")
     * @Method({"GET", "POST"})
     */
    public function modifierAvanceSpecialAction(Avance_Special $avance_Special)
    {
        $em = $this->getDoctrine()->getManager();

        $request = $this->get('request');

        if ($request->getMethod() == 'POST'){
            $avance_Special->setMontant($_POST['montant']);
            $avance_Special->setParMois($_POST['montantParMois']);

            $em->persist($avance_Special);

            $em->flush();

            //            ---HISTORIQUE GLOBAL-----

            $historiqueGlobal = new Historique_Global();

            $historiqueGlobal->setLibelle('Modification de l\'avance spécial de : '.$avance_Special->getEmpoye()->getNomComplet());
            $historiqueGlobal->setDate(new \DateTime());
            $historiqueGlobal->setLien('#');
            $historiqueGlobal->setUser($this->getUser());

            $em->persist($historiqueGlobal);

            $em->flush();
            //            ---HISTORIQUE GLOBAL-----

            return $this->redirectToRoute('avanceSpeciel_detail', array('id' => $avance_Special->getId()));
        }

        return new Response('Erreur!! 404 NOT FOUND');

    }

    /**
     * confirmer_demande .
     *
     * @Route("/confirmer/special/{id}", name="confirmer_avance_special")
     * @Method("GET")
     */
    public function DemandeConfirmeAction(Avance_Special $demande_Avance)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        if($demande_Avance->getUserConfirme1() == null){
            $demande_Avance->setUserConfirme1($user);
            $demande_Avance->setEtatDemande('En attente 2 eme confirmation');

        }else{

            if($demande_Avance->getUserConfirme1() != $this->getUser()){
                $demande_Avance->setUserConfirme2($user);
                $demande_Avance->setEtatDemande('En attente de comptabilisation');

            }

        }

        $demande_Avance->setUserRefuser(null);


        $em->persist($demande_Avance);

        //            ---HISTORIQUE GLOBAL-----

        $historiqueGlobal = new Historique_Global();

        $historiqueGlobal->setLibelle('Confirmation de la demande d\'avance spéciale: '.$demande_Avance->getNumero());
        $historiqueGlobal->setDate(new \DateTime());
        $historiqueGlobal->setLien($this->generateUrl('avanceSpeciel_detail', array('id' => $demande_Avance->getId())));
        $historiqueGlobal->setUser($this->getUser());

        $em->persist($historiqueGlobal);

        //            ---HISTORIQUE GLOBAL-----
        $em->flush();


//        REDIRECTION DE DOUBLE CONFIRMATION
        $config_demande = $em->getRepository('PaieBundle:ConfigurationDemande')->findOneBy(
            array('nom' => 'valeur_demande')
        );
        if($config_demande->getValeur() == 1){
            return $this->redirectToRoute('demande_avance_special_double_confirme', array('id' => $demande_Avance->getId()));
        }

        return $this->redirect($this->generateUrl('avanceSpeciel_detail', array('id' => $demande_Avance->getId())));

    }

    /**
     * REFUSER DEMANDE
     *
     * @Route("/refuser/specia/500/{id}", name="refuser_avance_special")
     * @Method("GET")
     */
    public function DemandeRefuserAction(Avance_Special $demande_Avance)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        $demande_Avance->setUserRefuser($user);

        $demande_Avance->setEtatDemande('Retour (Demande Refusée)');
        $demande_Avance->setDemander(null);

        $em->persist($demande_Avance);

        //            ---HISTORIQUE GLOBAL-----

        $historiqueGlobal = new Historique_Global();

        $historiqueGlobal->setLibelle('Refus de la demande: '.$demande_Avance->getNumero());
        $historiqueGlobal->setDate(new \DateTime());
        $historiqueGlobal->setLien($this->generateUrl('avanceSpeciel_detail', array('id' => $demande_Avance->getId())));
        $historiqueGlobal->setUser($this->getUser());

        $em->persist($historiqueGlobal);

        //            ---HISTORIQUE GLOBAL-----

        $em->flush();
        return $this->redirect($this->generateUrl('avanceSpeciel_detail', array('id' => $demande_Avance->getId())));

    }

    /**
     *  NOMBRE DE CONFIRMATION
     *
     * @Route("/special/{id}500/double-confirmation", name="demande_avance_special_double_confirme")
     * @Method("GET")
     */
    public function demandeDoubleConfirmeAction(Avance_Special $demande_Avance){
        $em = $this->getDoctrine()->getManager();

        $demande_Avance->setUserConfirme2($this->getUser());
        $demande_Avance->setEtatDemande('En attente de comptabilisation');

        $em->persist($demande_Avance);

        $em->flush();

        //            ---HISTORIQUE GLOBAL-----

        $historiqueGlobal = new Historique_Global();

        $historiqueGlobal->setLibelle('Double confirmation de la demande: '.$demande_Avance->getNumero());
        $historiqueGlobal->setDate(new \DateTime());
        $historiqueGlobal->setLien($this->generateUrl('avanceSpeciel_detail', array('id' => $demande_Avance->getId())));
        $historiqueGlobal->setUser($this->getUser());

        $em->persist($historiqueGlobal);
        $em->flush();

        //            ---HISTORIQUE GLOBAL-----

        if(isset($_GET['api'])){
            return new Response('OK');
        }

        return $this->redirect($this->generateUrl('avanceSpeciel_detail', array('id' => $demande_Avance->getId())));

    }


    /**
     *  NOMBRE DE CONFIRMATION
     *
     * @Route("/compt/{id}/500/comptabiliser", name="demande_avance_special_comptabiliser")
     * @Method({"GET", "POST"})
     */
    public function demandeComptabiliserAction(Avance_Special $demande_Avance){

        $em = $this->getDoctrine()->getManager();

        $request = $this->get('request');

        if($request->getMethod() == 'POST'){

            $user = $this->getUser();

            $demande_Avance->setUserCompta($user);
            $demande_Avance->setNomCaisse($_POST['caisse_debiter']);
            $demande_Avance->setEtatDemande('Demande acceptée');
            $demande_Avance->setEtat(true);
            $demande_Avance->setDemander(null);

            //----AJOUTER DEBIT DANS L'HISTORIQUE-----

//            $compte_Paie = $demande_Avance->getEmpoye()->getComptePaie();
//
//            $historique_paie = new Historique_Paie();
//            $historique_paie->setComptePaie($compte_Paie);
//
//            $historique_paie->setDate(new \DateTime());
//
//            $historique_paie->setMontant($demande_Avance->getMontant());
//
//            $historique_paie->setType('debit');
//
//
//            $historique_paie->setLibelle('Avance sur salaire: '.$demande_Avance->getNumero());
//
//            $em->persist($historique_paie);

            //----/////////AJOUTER DEBIT DANS L'HISTORIQUE/////////-----

            $em->persist($demande_Avance);

            $em->flush();

            $historiqueGlobal = new Historique_Global();

            $historiqueGlobal->setLibelle('Compatabilisation demande d\'avance spéciale '.$demande_Avance->getNumero());
            $historiqueGlobal->setDate(new \DateTime());
            $historiqueGlobal->setLien($this->generateUrl('formulaire_confirme', array('id' => $demande_Avance->getId())));
            $historiqueGlobal->setUser($this->getUser());
            $historiqueGlobal->setModification(null);

            $em->persist($historiqueGlobal);
            $em->flush();

            return $this->redirect($this->generateUrl('avanceSpeciel_detail', array('id' => $demande_Avance->getId())));
        }

        return new Response('Error 404');
    }



}

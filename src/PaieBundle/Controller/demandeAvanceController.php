<?php

namespace PaieBundle\Controller;

use HistoriqueGlobalBundle\Entity\Historique_Global;
use PaieBundle\Entity\Compte_Paie;
use PaieBundle\Entity\ConfigurationDemande;
use PaieBundle\Entity\Historique_Paie;
use PaieBundle\Entity\Numero_demande;
use PaieBundle\PaieBundle;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use EmployeBundle\Entity\Employe;
use PaieBundle\Entity\Demande_Avance;


/**
 * demandeAvance controller.
 *
 * @Route("/")
 */

class demandeAvanceController extends Controller
{
    /**
     * Formulaire_avance entities.
     *
     * @Route("/liste", name="liste_demande")
     * @Method("GET")
     */
    public function indexAction()
    {

        $em=$this->getDoctrine()->getManager();
        $repository_demandeAvance = $em->getRepository('PaieBundle:Demande_Avance');
        $demande_avances= $repository_demandeAvance->findBy(
            array(),
            array('date' => 'DESC')
        );

        $userActif = $this->getUser();

        if(!$userActif->ifRole('ROLE_PAIE')){
            $demande_avances = array();

            $listeAvance= $repository_demandeAvance->findBy(
                array(),
                array('date' => 'DESC')
            );

            $employeEnCour = $em->getRepository('EmployeBundle:Employe')->findBy(
                array('userCompte' => $userActif)
            );


            foreach ($listeAvance as $avance){
                if($avance->getUserCreer() == $userActif or $avance->getEmpoye() == $employeEnCour)
                    array_push($demande_avances, $avance);
            }

        }

        //---------------AVANCE SPECIAL-------
        $demandeAvanceSpecials = $em->getRepository('PaieBundle:Avance_Special')
            ->createQueryBuilder('avanceSpecial')
            ->where('avanceSpecial.demander = 1')
            ->getQuery()->getResult()
        ;

        return $this->render('@Paie/demandeAvance/liste_demande.html.twig', array(
            'demandes' => $demande_avances,
            'demandeAvanceSpecials' => $demandeAvanceSpecials,

        ));

    }
    /**
     * modifier_avance entities.
     *
     * @Route("/modifier-avance/ADF2{id}FA45", name="modifier_avance")
     * @Method({"GET", "POST"})
     */
    public function modifierAvanceAction(Request $request,Demande_Avance $demande)
    {
        /**
         * ajouter_avance entities.
         *
         * @Route("/avance", name="ajouter_avance")
         * @Method({"GET", "POST"})
         */
        if($request->getMethod() == 'POST'){

            $em=$this->getDoctrine()->getManager();
            $date = \DateTime::createFromFormat('d/m/Y H:i:s', $_POST['date_avance']);

            $demande->setDate(new \DateTime($date));

            $demande->setNumero($_POST['numero']);

            $demande->setMontant($_POST['montant']);

            $demande->setDescription($_POST['desc']);

            $em->merge($demande);

            $em->flush();
            return $this->redirect($this->generateUrl('formulaire_confirme', array('id' => $demande->getId())));

        }
        return $this->render('@Paie/demandeAvance/modification.html.twig', array(
            'demande' => $demande,
        ));



    }
    /**
     * ajouter_avance entities.
     *
     * @Route("/avance", name="ajouter_avance")
     * @Method({"GET", "POST"})
     */
    public function ajouterAvanceAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repository_employe = $em->getRepository('EmployeBundle:Employe') ;
        $num=$this->recupererNumero($em);

        $employes = $repository_employe->findBy(
            array('etat' => true),
            array('nomComplet' => 'ASC')
        );

        $userActif = $this->getUser();
        if (!$userActif->ifRole('ROLE_RH')){
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
        $demande_avance=new Demande_Avance();

        $usercreer=$this->getUser();

        if($request->getMethod() == 'POST'){
            $repository_numDemande = $em

                ->getRepository('PaieBundle:Numero_demande');

            $qNumDemande = $repository_numDemande->findOneBy(array('nom_config'=>'numero_demande_avance'));

            $numDemande = $qNumDemande;

            $dateJour  = new \DateTime();

            $numRetour = 'N° C-'.$numDemande->getNumeroDemande().'/'.$dateJour->format('y');

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
            $em->persist($numDemande);
            $em->flush();

            $employe = $em->getRepository('EmployeBundle:Employe')->findOneBy(array('nomComplet'=>$_POST['employe']));

            $date = \DateTime::createFromFormat('d/m/Y H:i:s', $_POST['date_avance']);

            $demande_avance->setUserCreer($usercreer);

            $demande_avance->setEmpoye($employe);

            $demande_avance->setDate(new \DateTime($date));

            $demande_avance->setNumero($_POST['numero']);

            $demande_avance->setMontant($_POST['montant']);

            $demande_avance->setDescription($_POST['desc']);

            $em->persist($demande_avance);

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

            return $this->redirect($this->generateUrl('formulaire_confirme', array('id' => $demande_avance->getId())));

        }
        return $this->render('@Paie/demandeAvance/ajouterDemande.html.twig', array(
            'employes' => $employes,
            'num'=>$num
        ));



    }

    public function recupererNumero($em){

        $repository_numDemande = $em

            ->getRepository('PaieBundle:Numero_demande');

        $numRetour = '';

        $qNumDemande = $repository_numDemande->findOneBy(array('nom_config'=>'numero_demande_avance'));

        $numDemande = $qNumDemande;

        $dateJour  = new \DateTime();

        $numRetour = 'N° C-'.$numDemande->getNumeroDemande().'/'.$dateJour->format('y');

        return $numRetour;

    }

    /**
     * formulaire_confirme .
     *
     * @Route("g/{id}/UY6T/formulaire", name="formulaire_confirme")
     * @Method("GET")
     */
    public function FormulaireConfirmeAction(Demande_Avance $demande_Avance)
    {

        $servIP = $this->getRequest()->server->get('SERVER_ADDR');

        return $this->render('@Paie/demandeAvance/show.html.twig',array(
            'demande' => $demande_Avance,
            'employe' => $demande_Avance->getEmpoye(),
            'servIP' => $servIP

        ));
    }

    /**
     * confirmer_demande .
     *
     * @Route("confirmer/{id}", name="confirmer_avance")
     * @Method("GET")
     */
    public function DemandeConfirmeAction(Demande_Avance $demande_Avance1)
    {
        $em = $this->getDoctrine()->getManager();

        $demande_Avance = $em->getRepository('PaieBundle:Demande_Avance')->findOneBy(array('id'=>$demande_Avance1));

        $user = $this->getUser();

        if($demande_Avance->getUserConfirme1() == null){
            $demande_Avance->setUserConfirme1($user);
            $demande_Avance->setEtat('En attente 2 eme confirmation');

            $demande_Avance->setModifiable(false);

        }else{

            if($demande_Avance->getUserConfirme1() != $this->getUser()){
                $demande_Avance->setUserConfirme2($user);
                $demande_Avance->setEtat('En attente de comptabilisation');

            }

        }

        $demande_Avance->setUserRefuser(null);

        $em->persist($demande_Avance);

        //            ---HISTORIQUE GLOBAL-----

        $historiqueGlobal = new Historique_Global();

        $historiqueGlobal->setLibelle('Confirmation de la demande: '.$demande_Avance->getNumero());
        $historiqueGlobal->setDate(new \DateTime());
        $historiqueGlobal->setLien($this->generateUrl('formulaire_confirme', array('id' => $demande_Avance->getId())));
        $historiqueGlobal->setUser($this->getUser());

        $em->persist($historiqueGlobal);

        //            ---HISTORIQUE GLOBAL-----
        $em->flush();


//        REDIRECTION DE DOUBLE CONFIRMATION
        $config_demande = $em->getRepository('PaieBundle:ConfigurationDemande')->findOneBy(
            array('nom' => 'valeur_demande')
        );
        if($config_demande->getValeur() == 1){
            return $this->redirectToRoute('demande_double_confirme', array('id' => $demande_Avance->getId()));
        }

        return $this->redirect($this->generateUrl('formulaire_confirme', array('id' => $demande_Avance->getId())));

    }

    /**
     * REFUSER DEMANDE
     *
     * @Route("refuser/{id}", name="refuser_avance")
     * @Method("GET")
     */
    public function DemandeRefuserAction(Demande_Avance $demande_Avance1)
    {
        $em = $this->getDoctrine()->getManager();

        $demande_Avance = $em->getRepository('PaieBundle:Demande_Avance')->findOneBy(array('id'=>$demande_Avance1));

        $user = $this->getUser();

        $demande_Avance->setUserRefuser($user);

        $demande_Avance->setEtat('Retour (Demande Refusée)');

        $em->persist($demande_Avance);

        //            ---HISTORIQUE GLOBAL-----

        $historiqueGlobal = new Historique_Global();

        $historiqueGlobal->setLibelle('Refus de la demande: '.$demande_Avance->getNumero());
        $historiqueGlobal->setDate(new \DateTime());
        $historiqueGlobal->setLien($this->generateUrl('formulaire_confirme', array('id' => $demande_Avance->getId())));
        $historiqueGlobal->setUser($this->getUser());

        $em->persist($historiqueGlobal);

        //            ---HISTORIQUE GLOBAL-----


        $em->flush();
        return $this->redirect($this->generateUrl('formulaire_confirme', array('id' => $demande_Avance->getId())));

    }

    /**
     * confirmer_demande .
     *
     * @Route("comptabiliser/{id}/demande", name="comptabilisation_demande_avance")
     *
     */
    public function ComptabiliserAction(Demande_Avance $demande_Avance1)
    {
        $em = $this->getDoctrine()->getManager();

        $request = $this->get('request');

        if($request->getMethod() == 'POST'){
            $demande_Avance = $em->getRepository('PaieBundle:Demande_Avance')->findOneBy(array('id'=>$demande_Avance1));

            $user = $this->getUser();

            $demande_Avance->setUserCompta($user);
            $demande_Avance->setNomCaisse($_POST['caisse_debiter']);
            $demande_Avance->setEtat('Demande acceptée');

            //----AJOUTER DEBIT DANS L'HISTORIQUE-----

            $compte_Paie = $demande_Avance->getEmpoye()->getComptePaie();

            $historique_paie = new Historique_Paie();
            $historique_paie->setComptePaie($compte_Paie);

            $historique_paie->setDate(new \DateTime());

            $historique_paie->setMontant($demande_Avance->getMontant());

            $historique_paie->setType('debit');


            $historique_paie->setLibelle('Avance sur salaire: '.$demande_Avance->getNumero());

            $em->persist($historique_paie);

            //----/////////AJOUTER DEBIT DANS L'HISTORIQUE/////////-----


            $em->persist($demande_Avance);

            $em->flush();

            $historiqueGlobal = new Historique_Global();

            $historiqueGlobal->setLibelle('Compatabilisation demande '.$demande_Avance->getNumero());
            $historiqueGlobal->setDate(new \DateTime());
            $historiqueGlobal->setLien($this->generateUrl('formulaire_confirme', array('id' => $demande_Avance->getId())));
            $historiqueGlobal->setUser($this->getUser());
            $historiqueGlobal->setModification(null);

            $em->persist($historiqueGlobal);
            $em->flush();

            return $this->redirect($this->generateUrl('formulaire_confirme', array('id' => $demande_Avance->getId())));
        }

        return new Response('Error 404');

    }

    /**
     * demande avance entities.
     *
     * @Route("/modifier_demande/{id}", name="modif_demande")
     * @Method({"GET", "POST"})
     */
    public function modifDemande(Demande_Avance $demande_avance)
    {
        $em=$this->getDoctrine()->getManager();
        $demande=$em->getRepository('PaieBundle:Demande_Avance')->find($demande_avance);
        $request = $this->get('request');

        if($request->getMethod() == 'POST'){

            $demande->setMontant($_POST['montant']);
            $demande->setDescription($_POST['libelle']);
            $em->persist($demande);
            $em->flush();

            return $this->redirect($this->generateUrl('liste_demande'));

        }
        return $this->render('PaieBundle:demande:modifDemande.html.twig',array('demande'=>$demande));
    }
    /**
     * demande_avance entities.
     *
     * @Route("/delete_demande/{id}", name="delete_demande")
     * @Method("GET")
     */
    public function deleteDemandeAction(Demande_Avance $demande_avance){

        $em=$this->getDoctrine()->getManager();

        $em->remove($demande_avance);
        $em->flush();
        return $this->redirect($this->generateUrl('liste_demande'));

    }

    /**
     * CONFIRMATION MULTIPLE
     *
     * @Route("/direction/confirmation-multiple", name="demandeAvance_multiple_confirmation")
     * @Method("GET")
     */
    public function confirmationMultipleAction(){

        $em = $this->getDoctrine()->getManager();

        $repository_demandeAvance = $em->getRepository('PaieBundle:Demande_Avance');

        $userActif = $this->getUser();

        $demandes = $repository_demandeAvance->findBy(
            array(),
            array('date' => 'DESC')
        );

//        return new Response(count($demandes));

        $demandeAvanceSpecials = $em->getRepository('PaieBundle:Avance_Special')
            ->createQueryBuilder('avanceSpecial')
            ->where('avanceSpecial.demander = 1')
            ->getQuery()->getResult()
        ;


        return $this->render('@Paie/demande/multiConfirme.html.twig', array(
            'demandes'  => $demandes,
            'demandeAvanceSpecials' => $demandeAvanceSpecials,
        ));
    }


    /**
     * API DEMANDE CONFIRME
     *
     * @Route("/demande-api/confirmer", name="api_demande_confirmer_avance")
     * @Method("POST")
     */
    public function APIConfirmerAction()
    {
        $em = $this->getDoctrine()->getManager();

        $demande_Avance = $em->getRepository('PaieBundle:Demande_Avance')->findOneBy(array(
            'id' => $_POST['id_demande']
            )
        );

        $user = $this->getUser();

        if($demande_Avance->getUserConfirme1() == null){
            $demande_Avance->setUserConfirme1($user);
            $demande_Avance->setEtat('En attente 2 eme confirmation');

            $demande_Avance->setModifiable(false);

        }else{

            if($demande_Avance->getUserConfirme1() != $this->getUser()){
                $demande_Avance->setUserConfirme2($user);
                $demande_Avance->setEtat('En attente de comptabilisation');

            }

        }

        $demande_Avance->setUserRefuser(null);

        $em->persist($demande_Avance);

        //            ---HISTORIQUE GLOBAL-----

        $historiqueGlobal = new Historique_Global();

        $historiqueGlobal->setLibelle('Confirmation de la demande: '.$demande_Avance->getNumero());
        $historiqueGlobal->setDate(new \DateTime());
        $historiqueGlobal->setLien($this->generateUrl('formulaire_confirme', array('id' => $demande_Avance->getId())));
        $historiqueGlobal->setUser($this->getUser());

        $em->persist($historiqueGlobal);

        //            ---HISTORIQUE GLOBAL-----

        $em->flush();

        //        REDIRECTION DE DOUBLE CONFIRMATION
        $config_demande = $em->getRepository('PaieBundle:ConfigurationDemande')->findOneBy(
            array('nom' => 'valeur_demande')
        );
        if($config_demande->getValeur() == 1){
            return $this->redirectToRoute('demande_double_confirme', array(
                'id' => $demande_Avance->getId(),
                'api' => 'OK',
            ));
        }

        return new Response('OK');

    }


    /**
     * API DEMANDE REFUSER
     *
     * @Route("api-demande/d/refuser", name="api_demande_refuser_avance")
     * @Method("POST")
     */
    public function APIDemandeRefuserAction()
    {
        $em = $this->getDoctrine()->getManager();

        $demande_Avance = $em->getRepository('PaieBundle:Demande_Avance')->findOneBy(array(
            'id'=> $_POST['id_demande']
            )
        );

        $user = $this->getUser();

        $demande_Avance->setUserRefuser($user);

        $demande_Avance->setEtat('Retour (Demande Refusée)');

        $em->persist($demande_Avance);

        //            ---HISTORIQUE GLOBAL-----

        $historiqueGlobal = new Historique_Global();

        $historiqueGlobal->setLibelle('Refus de la demande: '.$demande_Avance->getNumero());
        $historiqueGlobal->setDate(new \DateTime());
        $historiqueGlobal->setLien($this->generateUrl('formulaire_confirme', array('id' => $demande_Avance->getId())));
        $historiqueGlobal->setUser($this->getUser());

        $em->persist($historiqueGlobal);

        //            ---HISTORIQUE GLOBAL-----


        $em->flush();
        return new Response('OK');

    }

    /**
     *  NOMBRE DE CONFIRMATION
     *
     * @Route("/admin/confirmation", name="demande_configuration")
     * @Method({"GET", "POST"})
     */
    public function configurerDemandeAction(){
        $em = $this->getDoctrine()->getManager();

        $config_demande = $em->getRepository('PaieBundle:ConfigurationDemande')->findOneBy(
            array('nom' => 'valeur_demande')
        );

        if(! $config_demande){
            $config_demande = new ConfigurationDemande();
            $config_demande->setNom('valeur_demande');
            $config_demande->setValeur(2);

            $em->persist($config_demande);
            $em->flush();
        }

        $request = $this->get('request');

        if($request->getMethod() == 'POST'){
            $config_demande->setValeur($_POST['valeur']);

            $em->persist($config_demande);
            $em->flush();
        }


        return $this->render('@Paie/demandeAvance/configuration_demande.html.twig', array('valeur' => $config_demande->getValeur()));
    }

    /**
     *  NOMBRE DE CONFIRMATION
     *
     * @Route("/{id}500/double-confirmation", name="demande_double_confirme")
     * @Method("GET")
     */
    public function demandeDoubleConfirmeAction(Demande_Avance $demande_Avance){
        $em = $this->getDoctrine()->getManager();

        $demande_Avance->setUserConfirme2($this->getUser());
        $demande_Avance->setEtat('En attente de comptabilisation');

        $em->persist($demande_Avance);

        $em->flush();

        //            ---HISTORIQUE GLOBAL-----

        $historiqueGlobal = new Historique_Global();

        $historiqueGlobal->setLibelle('Double confirmation de la demande: '.$demande_Avance->getNumero());
        $historiqueGlobal->setDate(new \DateTime());
        $historiqueGlobal->setLien($this->generateUrl('formulaire_confirme', array('id' => $demande_Avance->getId())));
        $historiqueGlobal->setUser($this->getUser());

        $em->persist($historiqueGlobal);
        $em->flush();

        //            ---HISTORIQUE GLOBAL-----

        if(isset($_GET['api'])){
            return new Response('OK');
        }

        return $this->redirect($this->generateUrl('formulaire_confirme', array('id' => $demande_Avance->getId())));

    }

    /**
     * TOUT CONFIRMER
     *
     * @Route("/t/avance/tout-confirmer/200", name="demandeAvance_tout_confirmer")
     * @Method({"GET", "POST"})
     */
    public function toutConfirmerAction(){
        $em = $this->getDoctrine()->getManager();

        $repository_demandeAvance = $em->getRepository('PaieBundle:Demande_Avance');
        $demande_avances= $repository_demandeAvance->findAll();
        $user = $this->getUser();

        foreach ($demande_avances as $demande_Avance){
            if (!$demande_Avance->getUserConfirme2() and $demande_Avance->getUserConfirme1() != $user and !$demande_Avance->getUserRefuser()){
                if($demande_Avance->getUserConfirme1() == null){
                    $demande_Avance->setUserConfirme1($user);
                    $demande_Avance->setEtat('En attente 2 eme confirmation');

                    $demande_Avance->setModifiable(false);

                }else{

                    if($demande_Avance->getUserConfirme1() != $this->getUser()){
                        $demande_Avance->setUserConfirme2($user);
                        $demande_Avance->setEtat('En attente de comptabilisation');

                    }

                }

                $demande_Avance->setUserRefuser(null);

                $em->persist($demande_Avance);
                $em->flush();
                //            ---HISTORIQUE GLOBAL-----

                $historiqueGlobal = new Historique_Global();

                $historiqueGlobal->setLibelle('Confirmation de la demande: '.$demande_Avance->getNumero());
                $historiqueGlobal->setDate(new \DateTime());
                $historiqueGlobal->setLien($this->generateUrl('formulaire_confirme', array('id' => $demande_Avance->getId())));
                $historiqueGlobal->setUser($this->getUser());

                $em->persist($historiqueGlobal);

                //            ---HISTORIQUE GLOBAL-----
                $em->flush();

        //        REDIRECTION DE DOUBLE CONFIRMATION
                $config_demande = $em->getRepository('PaieBundle:ConfigurationDemande')->findOneBy(
                    array('nom' => 'valeur_demande')
                );
                if($config_demande->getValeur() == 1){
                    $demande_Avance->setUserConfirme2($this->getUser());
                    $demande_Avance->setEtat('En attente de comptabilisation');

                    $em->persist($demande_Avance);

                    $em->flush();

                    //            ---HISTORIQUE GLOBAL-----

                    $historiqueGlobal2 = new Historique_Global();

                    $historiqueGlobal2->setLibelle('Double confirmation de la demande: '.$demande_Avance->getNumero());
                    $historiqueGlobal2->setDate(new \DateTime());
                    $historiqueGlobal2->setLien($this->generateUrl('formulaire_confirme', array('id' => $demande_Avance->getId())));
                    $historiqueGlobal2->setUser($this->getUser());

                    $em->persist($historiqueGlobal2);
                    $em->flush();

                    //            ---HISTORIQUE GLOBAL-----


                }


            }
        }

        //----AVANCE SPECIAL-----
        $demandeAvanceSpecials = $em->getRepository('PaieBundle:Avance_Special')
            ->createQueryBuilder('avanceSpecial')
            ->where('avanceSpecial.demander = 1')
            ->andWhere('avanceSpecial.userRefuser is null')
            ->getQuery()->getResult()
        ;

        if (count($demandeAvanceSpecials) > 0){
            foreach ($demandeAvanceSpecials as $demandeAvanceSpecial){
                if($demandeAvanceSpecial->getUserConfirme1() == null){
                    $demandeAvanceSpecial->setUserConfirme1($user);
                    $demandeAvanceSpecial->setEtatDemande('En attente 2 eme confirmation');

                }else{

                    if($demandeAvanceSpecial->getUserConfirme1() != $this->getUser()){
                        $demandeAvanceSpecial->setUserConfirme2($user);
                        $demandeAvanceSpecial->setEtatDemande('En attente de comptabilisation');

                    }

                }

                $demandeAvanceSpecial->setUserRefuser(null);


                $em->persist($demande_Avance);

                //            ---HISTORIQUE GLOBAL-----

                $historiqueGlobal = new Historique_Global();

                $historiqueGlobal->setLibelle('Confirmation de la demande d\'avance spéciale: '.$demandeAvanceSpecial->getNumero());
                $historiqueGlobal->setDate(new \DateTime());
                $historiqueGlobal->setLien($this->generateUrl('avanceSpeciel_detail', array('id' => $demandeAvanceSpecial->getId())));
                $historiqueGlobal->setUser($this->getUser());

                $em->persist($historiqueGlobal);

                //            ---HISTORIQUE GLOBAL-----
                $em->flush();

                //        REDIRECTION DE DOUBLE CONFIRMATION
                $config_demande = $em->getRepository('PaieBundle:ConfigurationDemande')->findOneBy(
                    array('nom' => 'valeur_demande')
                );
                if($config_demande->getValeur() == 1){
                    $demandeAvanceSpecial->setUserConfirme2($this->getUser());
                    $demandeAvanceSpecial->setEtatDemande('En attente de comptabilisation');

                    $em->persist($demande_Avance);

                    $em->flush();

                    //            ---HISTORIQUE GLOBAL-----

                    $historiqueGlobal = new Historique_Global();

                    $historiqueGlobal->setLibelle('Double confirmation de la demande: '.$demandeAvanceSpecial->getNumero());
                    $historiqueGlobal->setDate(new \DateTime());
                    $historiqueGlobal->setLien($this->generateUrl('avanceSpeciel_detail', array('id' => $demandeAvanceSpecial->getId())));
                    $historiqueGlobal->setUser($this->getUser());

                    $em->persist($historiqueGlobal);
                    $em->flush();
                }
            }
        }

        return $this->redirectToRoute('liste_demande');



    }


}

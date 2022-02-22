<?php

namespace PaieBundle\Controller;

use HistoriqueGlobalBundle\Entity\Historique_Global;
use PaieBundle\Entity\ConfigurationDemande;
use PaieBundle\Entity\DemandePaiementConge;
use PaieBundle\Entity\Historique_Paie;
use PresenceBundle\Entity\Historique_Absence;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use PaieBundle\Entity\Demande_Avance;


/**
 * demandeAvance controller.
 *
 * @Route("/demande-paiement-congé")
 */

class demandePaiementCongeController extends Controller
{
    /**
     * Formulaire_avance entities.
     *
     * @Route("/", name="PaieBd_dpc_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em=$this->getDoctrine()->getManager();
        $repository_demandeAvance = $em->getRepository('PaieBundle:DemandePaiementConge');
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

        return $this->render('@Paie/DemandePaiementConge/index.html.twig', array(
            'demandes' => $demande_avances,

        ));

    }

    /**
     * ajouter_avance entities.
     *
     * @Route("/ajouter/demande", name="PaieBd_dpc_ajouter")
     * @Method({"GET", "POST"})
     */
    public function ajouterDemandeAction()
    {
        $em = $this->getDoctrine()->getManager();
        $repository_employe = $em->getRepository('EmployeBundle:Employe') ;

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


        if($request->getMethod() == 'POST'){
            $demande_dmp = new DemandePaiementConge();

            $demande_dmp->setDate(new \DateTime());
            $employe = $repository_employe->findOneBy(array('nomComplet' => $_POST['employe']));
            $demande_dmp->setEmpoye($employe);

            $demande_dmp->setUserCreer($userActif);
            $demande_dmp->setMontant($_POST['montant']);
            $demande_dmp->setNombreJour($_POST['nbJour']);
            $demande_dmp->setMotif($_POST['description']);

            $em->persist($demande_dmp);
            $em->flush();

            //            ---HISTORIQUE GLOBAL-----

            $historiqueGlobal = new Historique_Global();

            $historiqueGlobal->setLibelle('Création d\'une nouvelle demande de paiement de congé');
            $historiqueGlobal->setDate(new \DateTime());
            $historiqueGlobal->setLien($this->generateUrl('PaieBd_dpc_voir', array('id' => $demande_dmp->getId())));
            $historiqueGlobal->setUser($userActif);

            $em->persist($historiqueGlobal);

            //            ---HISTORIQUE GLOBAL-----

            $em->flush();

           return $this->redirectToRoute('PaieBd_dpc_voir', array('id' => $demande_dmp->getId()));

        }
        return $this->render('@Paie/DemandePaiementConge/ajouterDemande.html.twig', array(
            'employes' => $employes,
        ));



    }

    /**
     * formulaire_confirme .
     *
     * @Route("/{id}/UY6T/afficher", name="PaieBd_dpc_voir")
     * @Method("GET")
     */
    public function voirDemandeAction(DemandePaiementConge $demande)
    {
        $servIP = $this->getRequest()->server->get('SERVER_ADDR');

        return $this->render('@Paie/DemandePaiementConge/show.html.twig',array(
            'demande' => $demande,
            'employe' => $demande->getEmpoye(),
            'servIP' => $servIP

        ));
    }

    /**
     * confirmer_demande .
     *
     * @Route("/confirmer/{id}/500", name="PaieBd_dpc_confirmer")
     * @Method("GET")
     */
    public function DemandeConfirmeAction(DemandePaiementConge $demande_Avance)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        if($demande_Avance->getUserConfirme1() == null){
            $demande_Avance->setUserConfirme1($user);
            $demande_Avance->setEtat('En attente 2 eme confirmation');


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

        $historiqueGlobal->setLibelle('Confirmation de la demande de paiement de congé de: '.$demande_Avance->getEmpoye()->getNomComplet());
        $historiqueGlobal->setDate(new \DateTime());
        $historiqueGlobal->setLien($this->generateUrl('PaieBd_dpc_voir', array('id' => $demande_Avance->getId())));
        $historiqueGlobal->setUser($this->getUser());

        $em->persist($historiqueGlobal);

        //            ---HISTORIQUE GLOBAL-----
        $em->flush();


//        REDIRECTION DE DOUBLE CONFIRMATION
        $config_demande = $em->getRepository('PaieBundle:ConfigurationDemande')->findOneBy(
            array('nom' => 'valeur_demande')
        );
        if($config_demande->getValeur() == 1){
            return $this->redirectToRoute('PaieBd_dpc_doubleConfirmer', array('id' => $demande_Avance->getId()));
        }

        return $this->redirect($this->generateUrl('PaieBd_dpc_index'));


    }

    /**
     *  NOMBRE DE CONFIRMATION
     *
     * @Route("/_/{id}/500/double-confirmation", name="PaieBd_dpc_doubleConfirmer")
     * @Method("GET")
     */
    public function demandeDoubleConfirmeAction(DemandePaiementConge $demande_Avance){
        $em = $this->getDoctrine()->getManager();

        $demande_Avance->setUserConfirme2($this->getUser());
        $demande_Avance->setEtat('En attente de comptabilisation');

        $em->persist($demande_Avance);

        $em->flush();

        //            ---HISTORIQUE GLOBAL-----

        $historiqueGlobal = new Historique_Global();

        $historiqueGlobal->setLibelle('Double confirmation de la demande de paiement de congé de: '.$demande_Avance->getEmpoye()->getNomComplet());
        $historiqueGlobal->setDate(new \DateTime());
        $historiqueGlobal->setLien($this->generateUrl('PaieBd_dpc_voir', array('id' => $demande_Avance->getId())));
        $historiqueGlobal->setUser($this->getUser());

        $em->persist($historiqueGlobal);
        $em->flush();

        //            ---HISTORIQUE GLOBAL-----

        if(isset($_GET['api'])){
            return new Response('OK');
        }

        return $this->redirect($this->generateUrl('PaieBd_dpc_index'));

    }

    /**
     * REFUSER DEMANDE
     *
     * @Route("/refuser/{id}/500", name="PaieBd_dpc_refuser")
     * @Method("GET")
     */
    public function DemandeRefuserAction(DemandePaiementConge $demande_Avance)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->getUser();

        $demande_Avance->setUserRefuser($user);

        $demande_Avance->setEtat('Retour (Demande Refusée)');

        $em->persist($demande_Avance);

        //            ---HISTORIQUE GLOBAL-----

        $historiqueGlobal = new Historique_Global();

        $historiqueGlobal->setLibelle('Refus de la demande de paiement de congé de: '.$demande_Avance->getEmpoye()->getNomComplet());
        $historiqueGlobal->setDate(new \DateTime());
        $historiqueGlobal->setLien($this->generateUrl('PaieBd_dpc_voir', array('id' => $demande_Avance->getId())));
        $historiqueGlobal->setUser($this->getUser());

        $em->persist($historiqueGlobal);

        //            ---HISTORIQUE GLOBAL-----


        $em->flush();
        return $this->redirect($this->generateUrl('PaieBd_dpc_index'));


    }

    /**
     * Compatabilisation .
     *
     * @Route("/500/comptabiliser/{id}/demande", name="PaieBd_dpc_comptaliser")
     *
     */
    public function ComptabiliserAction(DemandePaiementConge $demande_Avance)
    {
        $em = $this->getDoctrine()->getManager();

        $request = $this->get('request');

        if($request->getMethod() == 'POST'){

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

            $historique_paie->setType('null');


            $historique_paie->setLibelle('Reçu paiement reste congé');

            $em->persist($historique_paie);

            //----/////////AJOUTER DEBIT DANS L'HISTORIQUE/////////-----

            //-----RESTE DU CONGE------

            $compte_Presence = $demande_Avance->getEmpoye()->getComptePresence();

            $resteConge = $compte_Presence->getResteConge() - $demande_Avance->getNombreJour();
            $compte_Presence->setResteConge($resteConge);

            $em->persist($compte_Presence);

            $historique_presence = new Historique_Absence();
            $historique_presence->setDate(new \DateTime());
            $historique_presence->setComptePresence($compte_Presence);
            $historique_presence->setDemandePaimenentConge($demande_Avance);

            $em->persist($historique_presence);

            //-----/////RESTE DU CONGE/////------

            $em->persist($demande_Avance);

            $em->flush();

            $historiqueGlobal = new Historique_Global();

            $historiqueGlobal->setLibelle('Compatabilisation paiement de congé de: '.$demande_Avance->getEmpoye()->getNomComplet());
            $historiqueGlobal->setDate(new \DateTime());
            $historiqueGlobal->setLien($this->generateUrl('PaieBd_dpc_voir', array('id' => $demande_Avance->getId())));
            $historiqueGlobal->setUser($this->getUser());
            $historiqueGlobal->setModification(null);

            $em->persist($historiqueGlobal);
            $em->flush();

            return $this->redirect($this->generateUrl('PaieBd_dpc_voir', array('id' => $demande_Avance->getId())));
        }

        return new Response('Error 404');

    }

}

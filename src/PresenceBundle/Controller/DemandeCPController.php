<?php

namespace PresenceBundle\Controller;

use DocumentBundle\Entity\Document;
use HistoriqueGlobalBundle\Entity\Historique_Global;
use PaieBundle\Entity\Historique_Paie;
use PresenceBundle\Entity\Conge_Permission;
use PresenceBundle\Entity\Demande_CP;
use PresenceBundle\Entity\Historique_Absence;
use PresenceBundle\Entity\Retard;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use PresenceBundle\Entity\Compte_Presence;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\BrowserKit\Request;
use Symfony\Component\HttpFoundation\Response;


/**
 * Compte Présence controller.
 *
 * @Route("/congé-permission")
 */
class DemandeCPController extends Controller
{
    /**
     * INDEX CP
     *
     * @Route("/", name="dcp_index")
     * @Method("GET")
     */
    public function indexAction(){
        $em = $this->getDoctrine()->getManager();

        $repository_dcp = $em->getRepository('PresenceBundle:Demande_CP') ;

        $dcps = $repository_dcp->findBy(
            array(),
            array('dateDebut' => 'ASC')
        );

        $userActif = $this->getUser();
        $employeEnCour = $em->getRepository('EmployeBundle:Employe')
            ->findOneBy(array('userCompte' => $userActif));

        if(!$userActif->ifRole('ROLE_RH')){
            $dcps = array();

            $listeDcps = $repository_dcp->findBy(
                array(),
                array('dateDebut' => 'ASC')
            );

            foreach ($listeDcps as $dp){
                if($dp->getEmploye() == $employeEnCour or $dp->getUserCreer() == $userActif )
                    array_push($dcps, $dp);
            }
        }

        return $this->render('@Presence/Demande_CP/index.html.twig', array(
            'dcps' => $dcps,
            'dateJour' => new \DateTime(),
        ));

    }

    /**
     * INDEX MULTIPLE CONFIRME
     *
     * @Route("/en-attente/confirmation", name="dcp_indexMultiple")
     * @Method({"GET", "POST"})
     */
    public function indexMultipleAction(){
        $em = $this->getDoctrine()->getManager();

        if(! $this->getUser()->ifRole('ROLE_ADMIN') and ! $this->getUser()->ifRole('ROLE_GRH') )
            return new Response('Erreur 404');

        $repository_dcp = $em->getRepository('PresenceBundle:Demande_CP') ;

        $dcps = $repository_dcp->findBy(
            array(),
            array('dateDemande' => 'DESC')
        );

        return $this->render('@Presence/Demande_CP/indexMultiConfirme.htm.twig', array(
            'demandes' => $dcps,
            'dateJour' => new \DateTime(),
        ));

    }

    /**
     * NOUVELLE DEMANDE CP
     *
     * @Route("/modification-congé-permission/ADF35{id}654CF", name="dcp_modifier")
     * @Method({"GET", "POST"})
     */
    public function modifierAction(Demande_CP $demande){
        $em = $this->getDoctrine()->getManager();
        $request = $this->get('request');
        $repository_employe = $em->getRepository('EmployeBundle:Employe') ;

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

        if($request->getMethod() == 'POST'){
            $user_atif = $this->getUser();
            $demande->setDateDemande(new \DateTime());

            $date_debut = \ DateTime::createFromFormat('d/m/Y', $_POST['date_debut_dcp']);
            $date_fin = \ DateTime::createFromFormat('d/m/Y', $_POST['date_fin_dcp']);
            $date_retour = \ DateTime::createFromFormat('d/m/Y', $_POST['date_retour_dcp']);
            $demande->setDateDebut($date_debut);
            $demande->setDateFin($date_fin);
            $demande->setDateRetour($date_retour);

            $demande->setNombreJour($_POST['intervalle_dcp']);

            $demande->setType($_POST['type_dcp']);

            $employe = $repository_employe->findOneBy(array(
                'nomComplet' => $_POST['employe']
            ));
            $demande->setEmploye($employe);

            if (isset($_POST['motif_dcp'])){
                $demande->setMotif($_POST['motif_dcp']);
            }
            $em->merge($demande);
            $em->flush();



            return $this->redirect($this->generateUrl('dcp_voir', array('id' => $demande->getId())));

        }
        return $this->render('@Presence/Demande_CP/modification.html.twig', array(
           'dcp' => $demande,
        ));

    }
    /**
     * NOUVELLE DEMANDE CP
     *
     * @Route("/nouvelle", name="dcp_ajouter")
     * @Method({"GET", "POST"})
     */
    public function ajouterAction(){
        $em = $this->getDoctrine()->getManager();

        $repository_employe = $em->getRepository('EmployeBundle:Employe') ;

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

        $request = $this->get('request');

        if($request->getMethod() == 'POST'){
            $user_atif = $this->getUser();

            $demande_cp = new Demande_CP();
            $demande_cp->setUserCreer($user_atif);

            $demande_cp->setDateDemande(new \DateTime());

            $date_debut = \ DateTime::createFromFormat('d/m/Y', $_POST['date_debut_dcp']);
            $date_fin = \ DateTime::createFromFormat('d/m/Y', $_POST['date_fin_dcp']);
            $date_retour = \ DateTime::createFromFormat('d/m/Y', $_POST['date_retour_dcp']);
            $demande_cp->setDateDebut($date_debut);
            $demande_cp->setDateFin($date_fin);
            $demande_cp->setDateRetour($date_retour);

            $demande_cp->setNombreJour($_POST['intervalle_dcp']);

            $demande_cp->setType($_POST['type_dcp']);

            $employe = $repository_employe->findOneBy(array(
                'nomComplet' => $_POST['employe']
            ));
            $demande_cp->setEmploye($employe);

            if (isset($_POST['motif_dcp'])){
                $demande_cp->setMotif($_POST['motif_dcp']);
            }

            $em->persist($demande_cp);
            $em->flush();

            $historiqueGlobal = new Historique_Global();

            $historiqueGlobal->setLibelle('Demande de Congé/Permission de l\'employé '.$employe->getNomComplet().' du '.$demande_cp->getDateDebut()->format('d/m/Y').' au '.$demande_cp->getDateFin()->format('d/m/Y'));
            $historiqueGlobal->setDate(new \DateTime());
            $historiqueGlobal->setLien($this->generateUrl('dcp_voir', array('id' => $demande_cp->getId())));
            $historiqueGlobal->setUser($this->getUser());
            $historiqueGlobal->setModification(null);

            $em->persist($historiqueGlobal);
            $em->flush();

            return $this->redirect($this->generateUrl('dcp_voir', array('id' => $demande_cp->getId())));

        }

        return $this->render('@Presence/Demande_CP/ajouter.html.twig', array(
            'employes' => $employes,
        ));

    }

    /**
     * RESPONSE PRIX DEMANDE
     *
     * @Route("/api/prix-demande", name="dcp_response_prix_demande")
     * @Method({"GET", "POST"})
     */
    public function responsePrixDemandeAction(){
        $em = $this->getDoctrine()->getManager();

        $repository_employe = $em->getRepository('EmployeBundle:Employe') ;

        $employe = $repository_employe->findOneBy(
            array('nomComplet' => $_POST['employe'])
        );

        $nbJour = $_POST['intervalle_dcp'];
        $resteConge = $employe->getComptePresence()->getResteConge();
        $retour = null;


        if($resteConge < $nbJour ){
            $surConge = $resteConge;
            $surSalaire = $nbJour - $surConge;

            $salaire = $employe->getSalaire();
            $salaire_jour = $salaire / 30;

            $prix = $salaire_jour * $surSalaire;

            $retour = array('prix' => number_format($prix,2, ',', ' ').' Ar');

            return new Response(json_encode($retour));
        }else
            return 'OK';

    }

    /**
     * UPLOAD FICHIER
     *
     * @Route("/{id}/fichier/upload", name="dcp_upload_fichier")
     * @Method({"GET", "POST"})
     */
    public function uploadDocumentAction(Demande_CP $demande_CP){
        $em = $this->getDoctrine()->getManager();

        $request = $this->get('request');

        if($request->getMethod() == 'POST'){
            $document = new Document();
            $document->setNom($_POST['nom']);

            $nomFichier = $document->uploadFichier('fichier');

            if($nomFichier){
                $document->setUrl($nomFichier);
            }else return new Response('Erreur!! Veuillez réessayer..');

            $document->setDemandeCP($demande_CP);

            $em->persist($document);
            $em->flush();

            return $this->redirect($this->generateUrl('dcp_voir', array(
                'id' => $demande_CP->getId()
            )));
        }

        return new Response('Erreur 404');
    }

    /**
     * INDEX CP
     *
     * @Route("/{id}/500/detail", name="dcp_voir")
     * @Method("GET")
     */
    public function voirAction(Demande_CP $demande_CP){
        $em = $this->getDoctrine()->getManager();

        $employe = $demande_CP->getEmploye();

        $repository_dccument = $em->getRepository('DocumentBundle:Document');

        $documents = $repository_dccument->findBy(
            array('demandeCP' => $demande_CP),
            array('nom' => 'asc')
        );

        $etat = '';

        if($demande_CP->getUserConfirme()){
            $etat = 'Confirmer';
        }

        if($demande_CP->getUserConfirme() and $this->getUser() == $demande_CP->getUserConfirme()){
            $etat = 'Confirmer par vous';
        }

        if($demande_CP->getUserRefuser()){
            $etat = 'Refuser';
        }

        return $this->render('@Presence/Demande_CP/voir.html.twig', array(
            'dcp' => $demande_CP,
            'etat' => $etat,
            'employe' => $employe,
            'documents' => $documents,
        ));
    }

    /**
     * CONFIRMER DEMANDE
     *
     * @Route("/{id}/500/confirmer", name="dcp_accepter")
     * @Method("GET")
     */
    public function confirmerAction(Demande_CP $demande_CP){
        $em = $this->getDoctrine()->getManager();

        $user_atif = $this->getUser();

        if (!$user_atif->ifRole('ROLE_ADMIN') and !$user_atif->ifRole('ROLE_GRH')) return new Response('Erreur 404');

        $demande_CP->setUserConfirme($user_atif);
        $demande_CP->setEtat('Demande confirmée');

        $demande_CP->setUserRefuser(null);

        $em->persist($demande_CP);
        $em->flush();

        //-----CREATION DANS L'ENTITE Conge_Permission--------
        $cp = new Conge_Permission();
        $cp->setDemandeCP($demande_CP);

        $cp->setDateDebut($demande_CP->getDateDebut());
        $cp->setDateRetour($demande_CP->getDateRetour());
        $cp->setDateFin($demande_CP->getDateFin());
        $cp->setDateDemande($demande_CP->getDateDemande());

        $cp->setNombreJour($demande_CP->getNombreJour());
        $cp->setType($demande_CP->getType());
        $cp->setMotif($demande_CP->getMotif());
        $cp->setEmploye($demande_CP->getEmploye());
        $em->persist($cp);
        $em->flush();

        //-----Historique---
            $historiqueAbs = new Historique_Absence();
            $historiqueAbs->setDate($cp->getDateDebut());
            $historiqueAbs->setCongePermission($cp);
            $historiqueAbs->setComptePresence($cp->getEmploye()->getComptePresence());
            $em->persist($historiqueAbs);
        //-----//////Historique//////---
        //-----/////////CREATION DANS L'ENTITE Conge_Permission/////////--------



        //--------SI CONGE SIMPLE---------

        if($cp->getType() == 'Congé'){
            $comptePresence = $cp->getEmploye()->getComptePresence();

            $nbJour = $demande_CP->getNombreJour();
            $resteConge = $comptePresence->getResteConge();

//            if($resteConge < $nbJour ){
//                $surConge = $resteConge;
//                $surSalaire = $nbJour - $surConge;
//
//                $salaire = $demande_CP->getEmploye()->getSalaire();
//                $salaire_jour = $salaire / 30;
//
//                $prix = $salaire_jour * $surSalaire;
//
//                //------------SUR SALAIRE------------
//
//                $compte_Paie = $cp->getEmploye()->getComptePaie();
//
//                $historique_paie = new Historique_Paie();
//                $historique_paie->setComptePaie($compte_Paie);
//
//                $historique_paie->setDate($cp->getDateDebut());
//
//                $historique_paie->setMontant($prix);
//
//                $historique_paie->setType('debit');
//                $historique_paie->setTri('congé');
//
//                $historique_paie->setLibelle('Prélèvement du congé sur salaire du '.$cp->getDateDebut()->format('d-m-Y').' au '.
//                    $cp->getDateRetour()->format('d-m-Y'));
//
//                $em->persist($historique_paie);
//
//                //------------///SUR SALAIRE///------------
//
//                //------------------SUR RESTE CONGE------------------
//
//                $comptePresence->setResteConge($resteConge - $surConge);
//
//                //------------------///SUR RESTE CONGE///------------------
//
//
//            }else{
//                $comptePresence->setResteConge($resteConge - $cp->getNombreJour());
//            }

            $resteActuel = $resteConge - $nbJour;

            $comptePresence->setResteConge($resteActuel);


            $em->persist($comptePresence);
        }

        //--------////SI CONGE SIMPLE////---------

        $em->flush();

        $historiqueGlobal = new Historique_Global();

        $historiqueGlobal->setLibelle('Confirmation de l\'employé '.$demande_CP->getEmploye()->getNomComplet().' du '.$demande_CP->getDateDebut()->format('d/m/Y').' au '.$demande_CP->getDateFin()->format('d/m/Y'));
        $historiqueGlobal->setDate(new \DateTime());
        $historiqueGlobal->setLien($this->generateUrl('dcp_voir', array('id' => $demande_CP->getId())));
        $historiqueGlobal->setUser($this->getUser());
        $historiqueGlobal->setModification(null);

        $em->persist($historiqueGlobal);
        $em->flush();

        return $this->redirect($this->generateUrl('dcp_voir', array(
            'id'=> $demande_CP->getId()
        )));
    }

    /**
     * REFUSER DEMANDE
     *
     * @Route("/{id}/refuser/404", name="dcp_refuser")
     * @Method({"GET", "POST"})
     */
    public function refuserAction(Demande_CP $demande_CP){
        $em = $this->getDoctrine()->getManager();

        $user_actif = $this->getUser();

        if (!$user_actif->ifRole('ROLE_ADMIN') and !$user_actif->ifRole('ROLE_GRH')) return new Response('Erreur 404');

        $demande_CP->setUserRefuser($user_actif);
        $demande_CP->setEtat('Demande Refusée');

        if(isset($_POST['texte_refuser'])) $demande_CP->setTexteRefuser($_POST['texte_refuser']);

        $em->persist($demande_CP);
        $em->flush();

        $historiqueGlobal = new Historique_Global();

        $historiqueGlobal->setLibelle('Refus de l\'employé '.$demande_CP->getEmploye()->getNomComplet().' du '.$demande_CP->getDateDebut()->format('d/m/Y').' au '.$demande_CP->getDateFin()->format('d/m/Y'));
        $historiqueGlobal->setDate(new \DateTime());
        $historiqueGlobal->setLien($this->generateUrl('dcp_voir', array('id' => $demande_CP->getId())));
        $historiqueGlobal->setUser($this->getUser());
        $historiqueGlobal->setModification(null);

        $em->persist($historiqueGlobal);
        $em->flush();

        return $this->redirect($this->generateUrl('dcp_voir', array(
            'id'=> $demande_CP->getId()
        )));
    }


    /**
     * API RESTE CONGE
     *
     * @Route("/reste/congé/c", name="api_reste_conge")
     * @Method({"GET", "POST"})
     */
    public function APIRestCongeAction(){
        $em = $this->getDoctrine()->getManager();

        if($this->get('request')->getMethod() == "POST"){
            $employe = $em->getRepository('EmployeBundle:Employe')->findOneBy(array(
                'nomComplet' => $_POST["nom_complet"]
            ));

            return new Response($employe->getComptePresence()->getResteConge());
        }

        return new Response('Erreur!');


    }

    /**
     * TOUT CONFIRMER
     *
     * @Route("/t/tout-confirmer/500", name="dcp_tout_confirmer")
     * @Method({"GET", "POST"})
     */
    public function toutConfirmerAction(){
        $em = $this->getDoctrine()->getManager();

        $repository_dcp = $em->getRepository('PresenceBundle:Demande_CP') ;
        $user_atif = $this->getUser();

        $dcps = $repository_dcp->findBy(
            array(),
            array('dateDemande' => 'DESC')
        );

        foreach ($dcps as $demande_CP){
            if(!$demande_CP->getUserConfirme() and !$demande_CP->getUserRefuser()){


                if (!$user_atif->ifRole('ROLE_ADMIN')) return new Response('Erreur 404');

                $demande_CP->setUserConfirme($user_atif);
                $demande_CP->setEtat('Demande confirmée');

                $demande_CP->setUserRefuser(null);

                $em->persist($demande_CP);
                $em->flush();

                //-----CREATION DANS L'ENTITE Conge_Permission--------
                $cp = new Conge_Permission();
                $cp->setDemandeCP($demande_CP);

                $cp->setDateDebut($demande_CP->getDateDebut());
                $cp->setDateRetour($demande_CP->getDateRetour());
                $cp->setDateFin($demande_CP->getDateFin());
                $cp->setDateDemande($demande_CP->getDateDemande());

                $cp->setNombreJour($demande_CP->getNombreJour());
                $cp->setType($demande_CP->getType());
                $cp->setMotif($demande_CP->getMotif());
                $cp->setEmploye($demande_CP->getEmploye());
                $em->persist($cp);
                $em->flush();

                //-----Historique---
                $historiqueAbs = new Historique_Absence();
                $historiqueAbs->setDate($cp->getDateDebut());
                $historiqueAbs->setCongePermission($cp);
                $historiqueAbs->setComptePresence($cp->getEmploye()->getComptePresence());
                $em->persist($historiqueAbs);
                //-----//////Historique//////---
                //-----/////////CREATION DANS L'ENTITE Conge_Permission/////////--------



                //--------SI CONGE SIMPLE---------
                if($cp->getType() == 'Congé'){
                    $comptePresence = $cp->getEmploye()->getComptePresence();
                    $reste = $comptePresence->getResteConge();
                    $comptePresence->setResteConge($reste - $cp->getNombreJour());

                    $em->persist($comptePresence);
                }
                //--------////SI CONGE SIMPLE////---------

                //--------SI CONGE SUR SALAIRE---------
                if($cp->getType() == 'Congé sur salaire'){
                    $compte_Paie = $cp->getEmploye()->getComptePaie();

                    $historique_paie = new Historique_Paie();
                    $historique_paie->setComptePaie($compte_Paie);

                    $historique_paie->setDate($cp->getDateDebut());

                    $prix = $cp->getEmploye()->getSalaire() / 30 * $cp->getNombreJour();

                    $historique_paie->setMontant($prix);

                    $historique_paie->setType('debit');
                    $historique_paie->setTri('congé');

                    $historique_paie->setLibelle('Prélèvement congé sur salaire du '.$cp->getDateDebut()->format('d-m-Y').' au '.
                        $cp->getDateRetour()->format('d-m-Y'));

                    $em->persist($historique_paie);
                }
                //--------////SI CONGE SIMPLE////---------

                $em->flush();

                $historiqueGlobal = new Historique_Global();

                $historiqueGlobal->setLibelle('Confirmation de l\'employé '.$demande_CP->getEmploye()->getNomComplet().' du '.$demande_CP->getDateDebut()->format('d/m/Y').' au '.$demande_CP->getDateFin()->format('d/m/Y'));
                $historiqueGlobal->setDate(new \DateTime());
                $historiqueGlobal->setLien($this->generateUrl('dcp_voir', array('id' => $demande_CP->getId())));
                $historiqueGlobal->setUser($this->getUser());
                $historiqueGlobal->setModification(null);

                $em->persist($historiqueGlobal);
                $em->flush();
            }
        }

        return $this->redirectToRoute('dcp_index');


    }



}

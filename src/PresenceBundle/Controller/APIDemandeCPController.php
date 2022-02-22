<?php

namespace PresenceBundle\Controller;


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
use Symfony\Component\HttpFoundation\Response;


/**
 * Compte Présence controller.
 *
 * @Route("/api/congé-permission")
 */
class APIDemandeCPController extends Controller
{
    /**
     * CONFIRMER DEMANDE
     *
     * @Route("/500/confirmer", name="api_dcp_accepter")
     * @Method({"GET", "POST"})
     */
    public function confirmerAction(){
        $em = $this->getDoctrine()->getManager();

        $demande_CP = $em->getRepository('PresenceBundle:Demande_CP')->findOneBy(array(
            'id' => $_POST['id_demande']
        ));

        $user_atif = $this->getUser();

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

        return new Response('OK');
    }

    /**
     * REFUSER DEMANDE
     *
     * @Route("/refuser/404", name="api_dcp_refuser")
     * @Method({"GET", "POST"})
     */
    public function refuserAction(){
        $em = $this->getDoctrine()->getManager();

        $demande_CP = $em->getRepository('PresenceBundle:Demande_CP')->findOneBy(array(
            'id' => $_POST['id_demande']
        ));

        $user_actif = $this->getUser();

        $demande_CP->setUserRefuser($user_actif);
        $demande_CP->setEtat('Demande Refusée');

        if(isset($_POST['texte_refuser'])) $demande_CP->setTexteRefuser($_POST['texte_refuser']);

        $em->persist($demande_CP);
        $em->flush();

        $historiqueGlobal = new Historique_Global();

        $historiqueGlobal->setLibelle('Confirmation de l\'employé '.$demande_CP->getEmploye()->getNomComplet().' du '.$demande_CP->getDateDebut()->format('d/m/Y').' au '.$demande_CP->getDateFin()->format('d/m/Y'));
        $historiqueGlobal->setDate(new \DateTime());
        $historiqueGlobal->setLien($this->generateUrl('dcp_voir', array('id' => $demande_CP->getId())));
        $historiqueGlobal->setUser($this->getUser());
        $historiqueGlobal->setModification(null);

        $em->persist($historiqueGlobal);
        $em->flush();

        return new Response('OK');
    }

    /**
     * TOUT CONFIRMER
     *
     * @Route("/changer/type", name="api_dcp_changer_type")
     * @Method({"GET", "POST"})
     */
    public function changerTypeDemandeAction(){

        $request = $this->get('request');

        if ($request->getMethod() == 'POST'){
            $em = $this->getDoctrine()->getManager();

            $demande_CP = $em->getRepository('PresenceBundle:Demande_CP')->findOneBy(array(
                'id' => $_POST['id_demande']
            ));

            $user_actif = $this->getUser();

            $demande_CP->setType($_POST['type']);

            $em->persist($demande_CP);
            $em->flush();

            $historiqueGlobal = new Historique_Global();

            $historiqueGlobal->setLibelle('Changement du type de la demande de l\'employé '.$demande_CP->getEmploye()->getNomComplet().' du '.$demande_CP->getDateDebut()->format('d/m/Y').' au '.$demande_CP->getDateFin()->format('d/m/Y'));
            $historiqueGlobal->setDate(new \DateTime());
            $historiqueGlobal->setLien($this->generateUrl('dcp_voir', array('id' => $demande_CP->getId())));
            $historiqueGlobal->setUser($this->getUser());
            $historiqueGlobal->setModification(null);

            $em->persist($historiqueGlobal);
            $em->flush();

            $employe = $demande_CP->getEmploye();

            $nbJour = $demande_CP->getNombreJour();
            $resteConge = $employe->getComptePresence()->getResteConge();
            $retour = null;


            if($demande_CP->getType() == "Congé"){
                if($resteConge < $nbJour ){
                    $surConge = $resteConge;
                    $surSalaire = $nbJour - $surConge;

                    $salaire = $employe->getSalaire();
                    $salaire_jour = $salaire / 30;

                    $prix = $salaire_jour * $surSalaire;

                    $retour = array('prix' => number_format($prix,2, ',', ' ').' Ar');

                    return new Response(json_encode($retour));
                }
            }


            return new Response(json_encode(array('OK')));

        }

    }

}

<?php

namespace PresenceBundle\Controller;

use DocumentBundle\Entity\Document;
use HistoriqueGlobalBundle\Entity\Historique_Global;
use PaieBundle\Entity\Historique_Paie;
use PresenceBundle\Entity\Absence;
use PresenceBundle\Entity\Historique_Absence;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use PresenceBundle\Entity\Compte_Presence;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;


/**
 * Compte Présence controller.
 *
 * @Route("/absence")
 */
class AbsenceController extends Controller
{

    /**
     * Signlaer une ABSENCE
     *
     * @Route("/signaler/{slug}", name="absence_signaler")
     * @Method({"GET", "POST"})
     */
    public function signalerAbsenceAction(Compte_Presence $compte_Presence)
    {

        $em = $this->getDoctrine()->getManager();
        $repository_employe = $em->getRepository('EmployeBundle:Employe');

        $employe = $repository_employe->findOneBy(array('comptePresence' => $compte_Presence));

        $request = $this->get('request');

        if($request->getMethod() == 'POST'){
            $absence = new Absence();

            $date_debut = \ DateTime::createFromFormat('d/m/Y', $_POST['date_debut_absence']);
            $date_retour = \ DateTime::createFromFormat('d/m/Y', $_POST['date_retour_absence']);

            $intervalle = $_POST['intervalle_absence'];

            $absence->setEmploye($employe);
            $absence->setDateDebut($date_debut);
            $absence->setDateRetour($date_retour);
            $absence->setNombreJour($intervalle);
            $absence->setUserCreer($this->getUser());

            if (isset($_POST['motif_absence'])) $absence->setMotif($_POST['motif_absence']);

            $em->persist($absence);

            //--------- ICI ON MET LE PAIE -------------

            $compte_Paie = $absence->getEmploye()->getComptePaie();

            $historique_paie = new Historique_Paie();
            $historique_paie->setComptePaie($compte_Paie);

            $historique_paie->setDate($absence->getDateDebut());
            $historique_paie->setAbsence($absence);
            $historique_paie->setNbJourAbsence($absence->getNombreJour());
            $historique_paie->setMontant($employe->getSalaire() / 30 * $historique_paie->getNbJourAbsence());

            $historique_paie->setType('absence');
            $historique_paie->setTri('absence');

            $historique_paie->setLibelle('Absence du '.$absence->getDateDebut()->format('d-m-Y').' au '.
                $absence->getDateRetour()->format('d-m-Y'));
            $em->persist($historique_paie);

            //-----HISTORIQUE ABSENCE----
            $historiqueAbs = new Historique_Absence();
            $historiqueAbs->setDate($date_debut);
            $historiqueAbs->setAbsence($absence);
            $historiqueAbs->setHistoriquePaieAbs($historique_paie);
            $historiqueAbs->setComptePresence($compte_Presence);

            $em->persist($historiqueAbs);

            $absence->setIdHPaie($historique_paie->getId());
            $em->persist($absence);
            $em->flush();

            $historiqueGlobal = new Historique_Global();

            $historiqueGlobal->setLibelle('Signalement absaence de l\'employé '.$employe->getNomComplet());
            $historiqueGlobal->setDate(new \DateTime());
            $historiqueGlobal->setLien($this->generateUrl('absence_voir', array('id' => $absence->getId())));
            $historiqueGlobal->setUser($this->getUser());
            $historiqueGlobal->setModification(null);

            $em->persist($historiqueGlobal);
            $em->flush();

            return $this->redirect($this->generateUrl('compte_employe_voir', array('slug' => $compte_Presence->getSlug())));

        }

        return new Response('Erreur 404');
    }

    /**
     * Signlaer une ABSENCE
     *
     * @Route("/signaler/par/personne", name="absence_signaler_personne")
     * @Method({"GET", "POST"})
     */
    public function signalerAbsenceByPersonneAction()
    {

        $em = $this->getDoctrine()->getManager();
        $repository_employe = $em->getRepository('EmployeBundle:Employe');

        $request = $this->get('request');

        if($request->getMethod() == 'POST'){

            $employe = $repository_employe->findOneBy(array(
                'nomComplet' => $_POST['employe']
            ));

            $compte_Presence = $employe->getComptePresence();

            $absence = new Absence();

            $date_debut = \ DateTime::createFromFormat('d/m/Y', $_POST['date_debut_absence']);
            $date_retour = \ DateTime::createFromFormat('d/m/Y', $_POST['date_retour_absence']);

            $intervalle = $_POST['intervalle_absence'];

            $absence->setEmploye($employe);
            $absence->setDateDebut($date_debut);
            $absence->setDateRetour($date_retour);
            $absence->setNombreJour($intervalle);
            $absence->setUserCreer($this->getUser());

            if (isset($_POST['motif_absence'])) $absence->setMotif($_POST['motif_absence']);

            $em->persist($absence);

            //--------- ICI ON MET LE PAIE -------------

            $compte_Paie = $absence->getEmploye()->getComptePaie();

            $historique_paie = new Historique_Paie();
            $historique_paie->setComptePaie($compte_Paie);

            $historique_paie->setDate($absence->getDateDebut());
            $historique_paie->setAbsence($absence);
            $historique_paie->setNbJourAbsence($absence->getNombreJour());
            $historique_paie->setMontant($employe->getSalaire() / 30 * $historique_paie->getNbJourAbsence());

            $historique_paie->setType('absence');
            $historique_paie->setTri('absence');

            $historique_paie->setLibelle('Absence du '.$absence->getDateDebut()->format('d-m-Y').' au '.
                $absence->getDateRetour()->format('d-m-Y'));
            $em->persist($historique_paie);

            //-----HISTORIQUE ABSENCE----
            $historiqueAbs = new Historique_Absence();
            $historiqueAbs->setDate($date_debut);
            $historiqueAbs->setAbsence($absence);
            $historiqueAbs->setHistoriquePaieAbs($historique_paie);
            $historiqueAbs->setComptePresence($compte_Presence);

            $em->persist($historiqueAbs);

            $absence->setIdHPaie($historique_paie->getId());
            $em->persist($absence);
            $em->flush();

            $historiqueGlobal = new Historique_Global();

            $historiqueGlobal->setLibelle('Signalement absaence de l\'employé '.$employe->getNomComplet());
            $historiqueGlobal->setDate(new \DateTime());
            $historiqueGlobal->setLien($this->generateUrl('absence_voir', array('id' => $absence->getId())));
            $historiqueGlobal->setUser($this->getUser());
            $historiqueGlobal->setModification(null);

            $em->persist($historiqueGlobal);
            $em->flush();

            return $this->redirect($this->generateUrl('absence_index'));

        }

        return new Response('Erreur 404');
    }

    /**
     * INDEX ABSENCE
     *
     * @Route("/liste", name="absence_index")
     * @Method("GET")
     */
    public function indexAction(){

        if(!$this->getUser()->ifRole('ROLE_RH') and !$this->getUser()->ifRole('ROLE_SEC')){
            return new Response('404 NOT-FOUND');
        }

        $em = $this->getDoctrine()->getManager();

        $repository_absence = $em->getRepository('PresenceBundle:Absence') ;

        $repository_retard = $em->getRepository('PresenceBundle:Retard') ;

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

        $absences = $repository_absence->findBy(
            array(),
            array('dateDebut' => 'DESC')
        );

        $retards = $repository_retard->findBy(
            array(),
            array('dateHeure' => 'DESC')
        );

        return $this->render('@Presence/Absence/index.html.twig', array(
            'absences' => $absences,
            'retards' => $retards,
            'employes' => $employes
        ));

    }

    /**
     * VOIR ABSENCE
     *
     * @Route("/{id}/afficher", name="absence_voir")
     * @Method("GET")
     */
    public function voirAction(Absence $absence){
        $em = $this->getDoctrine()->getManager();

        $repository_dccument = $em->getRepository('DocumentBundle:Document');

        $documents = $repository_dccument->findBy(
            array('absence' => $absence),
            array('nom' => 'asc')
        );

        $employe = $absence->getEmploye();
        $compte_Presence = $employe->getComptePresence();

        return $this->render('@Presence/Absence/voir.html.twig', array(
            'employe' => $employe,
            'comptePresence' => $compte_Presence,
            'absence' => $absence,
            'documents' => $documents,
        ));

    }

    /**
     * UPLOAD FICHIER
     *
     * @Route("/{id}/upload/fichier", name="absence_upload_fichier")
     * @Method({"GET", "POST"})
     */
    public function uploadDocumentAction(Absence $absence){
        $em = $this->getDoctrine()->getManager();

        $request = $this->get('request');

        if($request->getMethod() == 'POST'){
            $document = new Document();
            $document->setNom($_POST['nom']);

            $nomFichier = $document->uploadFichier('fichier');

            if($nomFichier){
                $document->setUrl($nomFichier);
            }else return new Response('Erreur!! Veuillez réessayer..');

            $document->setAbsence($absence);

            $em->persist($document);
            $em->flush();

            return $this->redirect($this->generateUrl('absence_voir', array(
                'id'=> $absence->getId()
            )));
        }

        return new Response('Erreur 404');
    }


    /**
     * CONFIRMER ABSENCE
     *
     * @Route("/500/{id}/confirmer", name="absence_accepter")
     * @Method({"GET", "POST"})
     */
    public function confirmerAction(Absence $absence){
        $em = $this->getDoctrine()->getManager();

        $user_atif = $this->getUser();

        if (!$user_atif->ifRole('ROLE_ADMIN')) return new Response('Erreur 404');

        $absence->setUserConfirme($user_atif);
        $absence->setEtat('Absence Justifiée');


//        SI ACTION DEJA REFUSER
        if($absence->getUserRefuser() and $absence->getIdHPaie()){
            $repository_HPaie = $em->getRepository('PaieBundle:Historique_Paie');
            $hpaie = $repository_HPaie->findOneBy(array(
               'id' => $absence->getIdHPaie()
            ));

            $em->remove($hpaie);
            $absence->setIdHPaie(null);
        }

        $absence->setUserRefuser(null);
        $historiqueAbs = $em->getRepository('PresenceBundle:Historique_Absence')->findOneBy(array('absence'=>$absence));
        $historiqueAbs->setHistoriquePaieAbs(null);
        $historiquePaie = $em->getRepository('PaieBundle:Historique_Paie')->findOneBy(array('id'=>$absence->getIdHPaie()));
        $absence->setIdHPaie(null);
        $em->persist($absence);
        $em->remove($historiquePaie);
        $em->flush();

        $historiqueGlobal = new Historique_Global();

        $historiqueGlobal->setLibelle('Confirmation de l\'absence de '.$absence->getEmploye()->getNomComplet().' du '.$absence->getDateDebut()->format('d/m/Y'));
        $historiqueGlobal->setDate(new \DateTime());
        $historiqueGlobal->setLien($this->generateUrl('absence_voir', array('id' => $absence->getId())));
        $historiqueGlobal->setUser($this->getUser());
        $historiqueGlobal->setModification(null);

        $em->persist($historiqueGlobal);
        $em->flush();

        return $this->redirect($this->generateUrl('absence_voir', array(
            'id'=> $absence->getId()
        )));
    }

    /**
     * REFUSER ABSENCE
     *
     * @Route("/{id}/400/confirmer", name="absence_refuser")
     * @Method({"GET", "POST"})
     */
    public function refuserAction(Absence $absence){
        $em = $this->getDoctrine()->getManager();

        $user_atif = $this->getUser();

        if (!$user_atif->ifRole('ROLE_ADMIN')) return new Response('Erreur 404');

        $absence->setUserRefuser($user_atif);
        $absence->setEtat('Justification Refusée');

        $em->persist($absence);
        $em->flush();

        $historiqueGlobal = new Historique_Global();

        $historiqueGlobal->setLibelle('Confirmation de l\'absence de '.$absence->getEmploye()->getNomComplet().' du '.$absence->getDateDebut()->format('d/m/Y'));
        $historiqueGlobal->setDate(new \DateTime());
        $historiqueGlobal->setLien($this->generateUrl('absence_voir', array('id' => $absence->getId())));
        $historiqueGlobal->setUser($this->getUser());
        $historiqueGlobal->setModification(null);

        $em->persist($historiqueGlobal);
        $em->flush();

        return $this->redirect($this->generateUrl('absence_voir', array(
            'id'=> $absence->getId()
        )));
    }

    /**
     * REFUSER ABSENCE
     *
     * @Route("/{id}/500/supprimer", name="absence_supprimer")
     * @Method({"GET", "POST"})
     */
    public function supprimerAction(Absence $absence){

        $em = $this->getDoctrine()->getManager();
        $historiqueAbs = $em->getRepository('PresenceBundle:Historique_Absence')->findOneBy(array('absence'=>$absence));

        if($historiqueAbs){
            $em->remove($historiqueAbs);

            $historiquePaieAbs = $historiqueAbs->getHistoriquePaieAbs();
            if($historiquePaieAbs){
                $em->remove($historiqueAbs);
            }
        }

        $historiquePaie = $em->getRepository('PaieBundle:Historique_Paie')->findOneBy(array('absence' => $absence));
        if ($historiquePaie)
            $em->remove($historiquePaie);

        $em->remove($absence);
        $em->flush();

        return $this->redirectToRoute('absence_index');
    }

    /**
     * MODIFIER ABSENCE
     *
     * @Route("/{id}/500/modifier", name="absence_modifier")
     * @Method({"GET", "POST"})
     */
    public function modifierAction(Absence $absence){

        $em = $this->getDoctrine()->getManager();
        $historiqueAbs = $em->getRepository('PresenceBundle:Historique_Absence')->findOneBy(array('absence'=>$absence));

        $paramComptePresence = clone $historiqueAbs->getComptePresence();
        if ($historiqueAbs->getHistoriquePaieAbs())
        {
            $historiqueAbs->setHistoriquePaieAbs(null);
            $historique_paie = $em->getRepository('PaieBundle:Historique_Paie')->findOneBy(array('id'=>$absence->getIdHPaie()));

            $date_debut = \ DateTime::createFromFormat('d/m/Y', $_POST['date_debut_absence']);
            $date_retour = \ DateTime::createFromFormat('d/m/Y', $_POST['date_retour_absence']);

            $intervalle = $_POST['intervalle_absence'];

            $absence->setDateDebut($date_debut);
            $absence->setDateRetour($date_retour);
            $absence->setNombreJour($intervalle);

            if (isset($_POST['motif_absence'])) $absence->setMotif($_POST['motif_absence']);
            $prix = $absence->getEmploye()->getSalaire() / 30 * $intervalle;

            $em->merge($absence);

            $historique_paie->setDate($date_debut);
            $historique_paie->setMontant($prix);
            $historique_paie->setLibelle('Absence du '.$absence->getDateDebut()->format('d-m-Y').' au '.
                $absence->getDateRetour()->format('d-m-Y'));

            $absence->setIdHPaie($historique_paie->getId());

            $historiqueAbs->setHistoriquePaieAbs($historique_paie);

            $em->merge($historiqueAbs);
            $em->merge($absence);
            $em->merge($historique_paie);
            $em->flush();
        }
        else
        {
            $date_debut = \ DateTime::createFromFormat('d/m/Y', $_POST['date_debut_absence']);
            $date_retour = \ DateTime::createFromFormat('d/m/Y', $_POST['date_retour_absence']);

            $intervalle = $_POST['intervalle_absence'];

            $absence->setDateDebut($date_debut);
            $absence->setDateRetour($date_retour);
            $absence->setNombreJour($intervalle);
            if (isset($_POST['motif_absence'])) $absence->setMotif($_POST['motif_absence']);
            $absence->setIdHPaie(null);
            $em->merge($absence);
            $em->flush();
        }
        return $this->redirectToRoute('compte_employe_voir',array('slug'=>$paramComptePresence->getSlug()));
    }
}

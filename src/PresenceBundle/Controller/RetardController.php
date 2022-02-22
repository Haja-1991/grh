<?php

namespace PresenceBundle\Controller;

use HistoriqueGlobalBundle\Entity\Historique_Global;
use PaieBundle\Entity\Historique_Paie;
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
 * @Route("/retard")
 */
class RetardController extends Controller
{

    private  function intervalleRetard(\DateTime $dateRetard){

        $dateTest = clone $dateRetard;

        $heure = intval($dateRetard->format('H'));

        if($heure >= 8 and $heure < 14){
            $dateTest->setTime(8, 0);

            $interval = $dateRetard->diff($dateTest);

            if($interval->h == 0) return $interval->i;

            return ($interval->h * 60) + $interval->i;

        }else{
            $dateTest->setTime(14, 0);

            $interval = $dateRetard->diff($dateTest);

            if($interval->h == 0) return $interval->i;

            return ($interval->h * 60) + $interval->i;
        }
    }

    /**
     * Signlaer un RETARD
     *
     * @Route("/signaler/{slug}", name="retard_signaler")
     * @Method({"GET", "POST"})
     */
    public function signalerRetardAction(Compte_Presence $compte_Presence)
    {

        $em = $this->getDoctrine()->getManager();
        $repository_employe = $em->getRepository('EmployeBundle:Employe');
        $repository_retard = $em->getRepository('PresenceBundle:Retard');

        $employe = $repository_employe->findOneBy(array('comptePresence' => $compte_Presence));

        $request = $this->get('request');

        if($request->getMethod() == 'POST'){

            $date_retard = \ DateTime::createFromFormat('d/m/Y H:i', $_POST['date_retard']);
            $testRetard = $repository_retard->findOneBy(array(
               'dateHeure' => $date_retard,
               'employe' => $employe
            ));

            if(!$testRetard){
                $retard = new Retard();

                $retard->setDateHeure($date_retard);

                $retard->setEmploye($employe);

                if (isset($_POST['motif_retard'])) $retard->setMotif($_POST['motif_retard']);

                $em->persist($retard);
                //---------ICI ON MET LE PAIE-------------

                $compte_Paie = $retard->getEmploye()->getComptePaie();

                $historique_paie = new Historique_Paie();
                $historique_paie->setComptePaie($compte_Paie);

                $historique_paie->setDate($retard->getDateHeure());

                $prix_minute = $retard->getEmploye()->getSalaire() / 30 / 7 / 60;

                $prix = $prix_minute * $this->intervalleRetard($retard->getDateHeure());

                $historique_paie->setMontant($prix);

                $historique_paie->setType('debit');
                $historique_paie->setTri('retard');

                $my_twig = $this->get('my_twig');

                $historique_paie->setLibelle('Retard du '.$retard->getDateHeure()->format('d-m-Y').' ('.
                    $my_twig->intervalleRetard($retard->getDateHeure()).')');

                $em->persist($historique_paie);
                $em->flush();

                //-----HISTORIQUE ABSENCE----

                $historiqueAbs = new Historique_Absence();
                $historiqueAbs->setDate($date_retard);
                $historiqueAbs->setHistoriquePaieAbs($historique_paie);
                $historiqueAbs->setRetard($retard);
                $historiqueAbs->setComptePresence($compte_Presence);
                $em->persist($historiqueAbs);

                //----------------------

                $em->flush();

                $historiqueGlobal = new Historique_Global();

                $historiqueGlobal->setLibelle('Signalement de retard de '.$employe->getNomComplet().' ('.
                    $my_twig->intervalleRetard($retard->getDateHeure()).')');
                $historiqueGlobal->setDate(new \DateTime());
                $historiqueGlobal->setLien($this->generateUrl('compte_employe_voir', array('slug' => $compte_Presence->getSlug())));
                $historiqueGlobal->setUser($this->getUser());
                $historiqueGlobal->setModification(null);

                $em->persist($historiqueGlobal);
                $em->flush();
            }


            return $this->redirect($this->generateUrl('compte_employe_voir', array('slug' => $compte_Presence->getSlug())));

        }

        return new Response('Erreur 404');
    }

    /**
     * Signlaer un RETARD
     *
     * @Route("/signaler/par/personne", name="retard_signaler_personne")
     * @Method({"GET", "POST"})
     */
    public function signalerRetardByPersonneAction()
    {

        $em = $this->getDoctrine()->getManager();
        $repository_employe = $em->getRepository('EmployeBundle:Employe');
        $repository_retard = $em->getRepository('PresenceBundle:Retard');

        $request = $this->get('request');
        if($request->getMethod() == 'POST'){
            $employe = $repository_employe->findOneBy(array(
                'nomComplet' => $_POST['employe']
            ));

            $date_retard = \ DateTime::createFromFormat('d/m/Y H:i', $_POST['date_retard']);
            $testRetard = $repository_retard->findOneBy(array(
                'dateHeure' => $date_retard,
                'employe' => $employe
            ));
            if(!$testRetard){
                $compte_Presence = $employe->getComptePresence();

                $retard = new Retard();

                $retard->setDateHeure($date_retard);

                $retard->setEmploye($employe);

                if (isset($_POST['motif_retard'])) $retard->setMotif($_POST['motif_retard']);

                $em->persist($retard);



                //---------ICI ON MET LE PAIE-------------

                $compte_Paie = $retard->getEmploye()->getComptePaie();

                $historique_paie = new Historique_Paie();
                $historique_paie->setComptePaie($compte_Paie);

                $historique_paie->setDate($retard->getDateHeure());

                $prix_minute = $retard->getEmploye()->getSalaire() / 30 / 7 / 60;

                $prix = $prix_minute * $this->intervalleRetard($retard->getDateHeure());

                $historique_paie->setMontant($prix);

                $historique_paie->setType('debit');
                $historique_paie->setTri('retard');

                $my_twig = $this->get('my_twig');

                $historique_paie->setLibelle('Retard du '.$retard->getDateHeure()->format('d-m-Y').' ('.
                    $my_twig->intervalleRetard($retard->getDateHeure()).')');

                $em->persist($historique_paie);

                //-----HISTORIQUE ABSENCE----
                $historiqueAbs = new Historique_Absence();
                $historiqueAbs->setDate($date_retard);
                $historiqueAbs->setRetard($retard);
                $historiqueAbs->setHistoriquePaieAbs($historique_paie);
                $historiqueAbs->setComptePresence($compte_Presence);
                $em->persist($historiqueAbs);
                $em->flush();

                //----------------------

                //-----------------HISTORIQUE GLOBAL-------------------
                $historiqueGlobal = new Historique_Global();

                $historiqueGlobal->setLibelle('Signalement de retard de '.$employe->getNomComplet().' ('.
                    $my_twig->intervalleRetard($retard->getDateHeure()).')');
                $historiqueGlobal->setDate(new \DateTime());
                $historiqueGlobal->setLien($this->generateUrl('compte_employe_voir', array('slug' => $compte_Presence->getSlug())));
                $historiqueGlobal->setUser($this->getUser());
                $historiqueGlobal->setModification(null);

                $em->persist($historiqueGlobal);
                $em->flush();
            }

            return $this->redirect($this->generateUrl('absence_index'));

        }

        return new Response('Erreur 404');
    }

    /**
     * voir un RETARD
     *
     * @Route("/afficher/{id}/info", name="retard_afficher")
     * @Method({"GET", "POST"})
     */
    public function ShowRetardAction(Historique_Absence $historiqueAbsence)
    {
        $em = $this->getDoctrine()->getManager();
        $employe = $historiqueAbsence->getRetard()->getEmploye();

        return $this->render('@Presence/Retard/voir.html.twig', array(
            'historique_abs' => $historiqueAbsence,
            'employe' => $employe,));

    }
    /**
     * supprimer un RETARD
     *
     * @Route("/supprimer/{id}/info", name="retard_supprimer")
     * @Method({"GET", "POST"})
     */
    public function DeleteRetardAction(Historique_Absence $historiqueAbsence)
    {
        $em = $this->getDoctrine()->getManager();

        if($historiqueAbsence->getHistoriquePaieAbs()){
            $em->remove($historiqueAbsence->getHistoriquePaieAbs());
        }

        if($historiqueAbsence->getRetard()){
            $em->remove($historiqueAbsence->getRetard());
        }

        $em->remove($historiqueAbsence);
        $em->flush();

       return $this->redirectToRoute('compte_employe_voir',array('slug'=>$historiqueAbsence->getComptePresence()->getSlug()));

    }
}

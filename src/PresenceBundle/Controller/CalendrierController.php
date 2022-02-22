<?php

namespace PresenceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;


/**
 * Compte Présence controller.
 *
 * @Route("/calendrier")
 */
class CalendrierController extends Controller
{

    /**
     * Calendrier GLOBAL
     *
     * @Route("/", name="calendrier_index")
     * @Method("GET")
     */
    public function toutAfficherAction(){

        return $this->render('@Presence/Calendrier/voir.html.twig');
    }

    /**
     * Calendrier GLOBAL
     *
     * @Route("/rest/tout", name="calendrier_api_tout")
     * @Method("GET")
     */
    public function restApiToutAction()
    {

        $em = $this->getDoctrine()->getManager();

        $events = array();

        $repository_retard = $em->getRepository('PresenceBundle:Retard');
        $repository_absence = $em->getRepository('PresenceBundle:Absence');
        $repository_conge = $em->getRepository('PresenceBundle:Conge_Permission');

        $retards = $repository_retard->findNotFalseEmploye();
        $absences = $repository_absence->findNotFalseEmploye();
        $conges = $repository_conge->findNotFalseEmploye();

//        $retard = new Retard();
//        $absence = new Absence();
//        $cp = new Conge_Permission();

        if(count($retards) > 0){
            foreach ($retards as $retard){
                $nom = $retard->getEmploye()->getPrenom().' '.$retard->getEmploye()->getNom()[0].'.';
                if(!$retard->getEmploye()->getPrenom()) $nom = $retard->getEmploye()->getNom();
                $inc = array(
                    'title' => 'Retard: '.$nom,
                    'start' => $retard->getDateHeure()->format('Y-m-d').'T'.$retard->getDateHeure()->format('H:i:s').'.00',
                    'color' => '#e68a00',
                );
                array_push($events, $inc);
            }
        }

        if(count($absences) > 0){
            foreach ($absences as $absence){
                $nom = $absence->getEmploye()->getPrenom().' '.$absence->getEmploye()->getNom()[0].'.';
                if(!$absence->getEmploye()->getPrenom()) $nom = $absence->getEmploye()->getNom();
                $inc = array(
                    'title' => 'Absence: '.$nom,
                    'start' => $absence->getDateDebut()->format('Y-m-d'),
                    'end' => $absence->getDateRetour()->format('Y-m-d'),
                    'color' => '#e62e00',
                    'url' => $this->generateUrl('absence_voir', array('id' => $absence->getId()) )
                );
                array_push($events, $inc);
            }
        }

        if(count($conges) > 0){
            foreach ($conges as $conge){
                $nom = $conge->getEmploye()->getPrenom().' '.$retard->getEmploye()->getNom()[0].'.';
                if(!$conge->getEmploye()->getPrenom()) $nom = $conge->getEmploye()->getNom();
                $inc = array(
                    'title' => 'Congés: '.$nom,
                    'start' => $conge->getDateDebut()->format('Y-m-d'),
                    'end' => $conge->getDateRetour()->format('Y-m-d'),
                    'color' => '#004d99',
                    'url' => $this->generateUrl('dcp_voir', array('id' => $conge->getDemandeCP()->getId() ))

                );
                array_push($events, $inc);
            }
        }




        return new Response(json_encode($events));

    }

}

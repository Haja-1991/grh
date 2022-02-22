<?php

namespace PresenceBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use EmployeBundle\Entity\Employe;
use PresenceBundle\Entity\Absence;
use PresenceBundle\Entity\Retard;


/**
 * Graph Absence controller.
 *
 * @Route("/graphique")
 */
class GraphController extends Controller
{
    /**
     * INDEX GRAPH ABSENCE
     *
     * @Route("/absence", name="graph_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        if(!$this->getUser()->ifRole('ROLE_RH')) return new Response('404 NOT-FOUND');

        $em = $this->getDoctrine()->getManager();
        $repository_employe = $em->getRepository('EmployeBundle:Employe');
        $dateActuel = new \DateTime();
        $annee = $dateActuel->format('Y');
        if ( isset($_GET['année'])){

            $annee = $_GET['année'];
        }

        $employes = $repository_employe->findBy(
            array('etat' => true),
            array('nom' => 'asc')
        );

        return $this->render('@Presence/graph/graphAsence.html.twig',array('employes'=>$employes,'annee'=>$annee));
    }
    /**
     * LISTE DONNEE
     *
     * @Route("/data/histo.json", name="graph_datas")
     * @Method("GET")
     */
    public function dataGraphAction()
    {
        if(!$this->getUser()->ifRole('ROLE_RH')) return new Response('404 NOT-FOUND');

        $em = $this->getDoctrine()->getManager();
        $repository_employe = $em->getRepository('EmployeBundle:Employe');

        $employes = $repository_employe->findBy(
            array('etat' => true),
            array('id' => 'asc')
        );
        $repository_absence=$em->getRepository('PresenceBundle:Absence');

        $repository_retard= $em->getRepository('PresenceBundle:Retard');


        $dateActuel = new \DateTime();
        $annee = $dateActuel->format('Y');

        if (  isset($_GET['annee'])){

            $annee = $_GET['annee'];
        }



        $dateDebut = \ DateTime::createFromFormat('d/m/Y', '1/1/'.$annee)->format('Y-m-d');
        $dateFin = \ DateTime::createFromFormat('d/m/Y', '31/12/'.$annee)->format('Y-m-d');

        $dataArray=array(array("",""));

        foreach($employes as $employe)
            {
                $nbrAbs =0;
                $nbrRet =0;
                $nbrSomme=0;
                $absences=$repository_absence->findAbsByAnnee($employe,$dateDebut,$dateFin);
                foreach ($absences as $absence)
                {
                    if($absence->getDateDebut()->format('Y')==$annee and $absence->getEmploye()->getNom() == $employe->getNom()  ){
                        $nbrAbs++;
                    }
                }
                 $retards=$repository_retard->findRetByAnnee($employe,$dateDebut,$dateFin);
                foreach ($retards as $retard)
                {
                    if($retard->getDateHeure()->format('Y')==$annee and  $retard->getEmploye()->getNom() == $employe->getNom()){
                        $nbrRet++;
                    }
                }
                $nbrSomme=$nbrAbs+$nbrRet;
                if($nbrSomme>0){
                    array_push($dataArray,array(
                        $employe->getNomComplet().'( Rétard ='.$nbrRet.' et Absence = '.$nbrAbs.')',
                        intval($nbrSomme) ,


                    ));
                }


            }

        return new Response(json_encode($dataArray));
    }
}

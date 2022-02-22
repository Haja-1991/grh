<?php

namespace PresenceBundle\Controller;

use PresenceBundle\Entity\Historique_Absence;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use DocumentBundle\Entity\Document;
use PresenceBundle\Entity\Compte_Presence;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use EmployeBundle\Entity\Employe;
use EmployeBundle\Form\EmployeType;
use Symfony\Component\HttpFoundation\Response;



/**
 * Compte Présence controller.
 *
 * @Route("/")
 */
class comptePresenceController extends Controller
{

    /**
     * Liste de employé.
     *
     * @Route("/", name="compte_employe_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        if(!$this->getUser()->ifRole('ROLE_RH')) return new Response('404 NOT-FOUND');

        $em = $this->getDoctrine()->getManager();
        $repository_employe = $em->getRepository('EmployeBundle:Employe');

        $employes = $repository_employe->findBy(
            array('etat' => true),
            array('nom' => 'asc')
        );

        return $this->render('@Presence/comptePresence/index.html.twig', array(
            'employes' => $employes
        ));
    }

    /**
     * Liste de employé.
     *
     * @Route("/{slug}/détails", name="compte_employe_voir")
     * @Method("GET")
     */
    public function afficherAction(Compte_Presence $compte_Presence)
    {
        $em = $this->getDoctrine()->getManager();
        $repository_employe = $em->getRepository('EmployeBundle:Employe');
        $repository_historiqueAbs = $em->getRepository('PresenceBundle:Historique_Absence');

        $employe = $repository_employe->findOneBy(array('comptePresence' => $compte_Presence));

        // TRI PAR DATE
        $dateActuel = new \DateTime();
        $annee = $dateActuel->format('Y');

        if(isset($_GET['année'])){
            $annee = $_GET['année'];
        }

        $dateDebut = \ DateTime::createFromFormat('d/m/Y', '1/1/'.$annee)->format('Y-m-d');
        $dateFin = \ DateTime::createFromFormat('d/m/Y', '31/12/'.$annee)->format('Y-m-d');

        $historiqueAbss = $repository_historiqueAbs->findByAnne($compte_Presence->getSlug(), $dateDebut, $dateFin);


        return $this->render('@Presence/comptePresence/voir.html.twig', array(
            'employe' => $employe,
            'comptePresence' => $compte_Presence,
            'historiqueAbss' => $historiqueAbss,
            'annee' => $annee,
        ));
    }

    /**
     * Liste de employé.
     *
     * @Route("/modifier/reste-congé", name="compte_presence_modifierResteCongé")
     *
     */
    public function modifierResteAction(Request $request)
    {

        if(!$this->getUser()->ifRole('ROLE_RH')) return new Response('404 NOT-FOUND');


        $em = $this->getDoctrine()->getManager();
        $repository_employe = $em->getRepository('EmployeBundle:Employe');

        $employes = $repository_employe->findBy(
            array('etat' => true),
            array('nom' => 'asc')
        );

        if($request->getMethod() == "POST"){
            foreach ($employes as $employe){
                $comptePresence = $employe->getComptePresence();
                $tmpReste = $_POST[''.$comptePresence->getId()];
                if ($tmpReste != $comptePresence->getResteConge()){
                    $comptePresence->setResteConge($tmpReste);
                    $em->persist($comptePresence);
                }
            }

            $em->flush();

            return new Response('Base de donnée mis à jour.');
        }

        return $this->render('@Presence/comptePresence/modifier_resteConge.html.twig', array(
            'employes' => $employes
        ));




    }



}

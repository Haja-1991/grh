<?php

namespace HistoriqueGlobalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


/**
 * Produit controller.
 *
 * @Route("/api/externe")
 */
class ExterneAPIController extends Controller
{

    /**
     * @Route("/d/nombre-demande-avance", name="_ext_demandeAvance")
     */
    public function nombreDemandeAvanceAction(){
        $em = $this->getDoctrine()->getManager();

        $repository_demande = $em->getRepository('PaieBundle:Demande_Avance');

        $count = 0;

        $demandes = array();

        foreach ($d = $repository_demande->findAll() as $demande){
            if(! $demande->getUserConfirme2() and ! $demande->getUserRefuser())
                array_push($demandes, $demande);
        }

        if($demandes)
            $count = count($demandes);

        //----AVANCE SPECIAL-----
        $demandeAvanceSpecials = $em->getRepository('PaieBundle:Avance_Special')
            ->createQueryBuilder('avanceSpecial')
            ->where('avanceSpecial.demander = 1')
            ->andWhere('avanceSpecial.userRefuser is null')
            ->getQuery()->getResult()
        ;

        if($demandeAvanceSpecials){
            $count = $count + count($demandeAvanceSpecials);
        }

        $response = new Response();
        $response->setContent(json_encode(array(
            'nombre' => $count
        )));
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/c/demande-conge", name="_ext_demandeConge")
     */
    public function nombreDemandeConge(){
        $em = $this->getDoctrine()->getManager();

        $repository_demande = $em->getRepository('PresenceBundle:Demande_CP');

        $count = 0;

        $demandes = array();

        foreach ($d = $repository_demande->findAll() as $demande){
            if(! $demande->getUserRefuser() and ! $demande->getUserConfirme())
                array_push($demandes, $demande);
        }

        if($demandes)
            $count = count($demandes);

        $response = new Response();
        $response->setContent(json_encode(array(
            'nombre' => $count
        )));
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/demande/paiement/nombre/500", name="_grh_demandePaiementConge")
     */
    public function nombreDemandePaiementCongeAction(){
        $em = $this->getDoctrine()->getManager();

        $repository_demande = $em->getRepository('PaieBundle:DemandePaiementConge');

        $count = 0;

        $demandes = array();

        foreach ($d = $repository_demande->findAll() as $demande){
            if(! $demande->getUserRefuser() and ! $demande->getUserConfirme2())
                array_push($demandes, $demande);
        }

        if($demandes)
            $count = count($demandes);

        $response = new Response();
        $response->setContent(json_encode(array(
            'nombre' => $count
        )));
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }

    /**
     * @Route("/liste/personne/actif", name="_grh_demandePaiementConge")
     */
    public function listeEmployerAPIAction(){
        $em = $this->getDoctrine()->getManager();
        $repositoryEmploye = $em->getRepository('EmployeBundle:Employe');

        $listeEmp = $repositoryEmploye->findBy(
            array('etat' => true),
            array('nom' => 'asc')
        );

        $employes = array();

        foreach ($listeEmp as $employe){
            array_push($employes, array(
                'id' => $employe->getId(),
                'nom' => $employe->getNomComplet(),
                'poste' => $employe->getPoste(),
                'lien' => $this->generateUrl('employe_voir', array('slug' =>  $employe->getSlug()),  UrlGeneratorInterface::ABSOLUTE_URL)

            ));
        }

        $response = new Response();
        $response->setContent(json_encode($employes));
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Content-Type', 'application/json');

        return $response;
    }



}

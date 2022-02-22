<?php

namespace HistoriqueGlobalBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;


/**
 * Produit controller.
 *
 * @Route("/api/hissab")
 */
class HissabAPIController extends Controller
{

    /**
     * @Route("/d/nombre-demande-depense", name="_grh_demandeAvance")
     */
    public function nombreDemandeAvanceAction(){
        $em = $this->getDoctrine()->getManager();

        $repository_demande = $em->getRepository('PaieBundle:Demande_Avance');

        $userActif = $em->getRepository('UserBundle:User')->findOneBy(array(
            'username' => $_POST['username']
        ));

        $count = 0;

        $demandes = array();

        foreach ($d = $repository_demande->findAll() as $demande){
            if($demande->getUserConfirme1() != $userActif and  ! $demande->getNomCaisse() and ! $demande->getUserConfirme2())
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

        return new Response($count);
    }

    /**
     * @Route("/c/demande/conge", name="_grh_demandeConge")
     */
    public function nombreDemandeConge(){
        $em = $this->getDoctrine()->getManager();

        $repository_demande = $em->getRepository('PresenceBundle:Demande_CP');

        $userActif = $em->getRepository('UserBundle:User')->findOneBy(array(
            'username' => $_POST['username']
        ));

        $count = 0;

        $demandes = array();

        foreach ($d = $repository_demande->findAll() as $demande){
            if(! $demande->getUserRefuser() and ! $demande->getUserConfirme())
                array_push($demandes, $demande);
        }

        if($demandes)
            $count = count($demandes);

        return new Response($count);
    }

    /**
     * @Route("/demande/paiement/nombre/500", name="_grh_demandePaiementConge")
     */
    public function nombreDemandePaiementCongeAction(){
        $em = $this->getDoctrine()->getManager();

        $repository_demande = $em->getRepository('PaieBundle:DemandePaiementConge');

        $userActif = $em->getRepository('UserBundle:User')->findOneBy(array(
            'username' => $_POST['username']
        ));

        $count = 0;

        $demandes = array();

        foreach ($d = $repository_demande->findAll() as $demande){
            if($demande->getUserConfirme1() != $userActif and  ! $demande->getNomCaisse() and ! $demande->getUserConfirme2())
                array_push($demandes, $demande);
        }

        if($demandes)
            $count = count($demandes);

        return new Response($count);
    }

}

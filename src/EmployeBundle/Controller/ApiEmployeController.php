<?php


namespace EmployeBundle\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Employe controller.
 *
 * @Route("/api")
 */
class ApiEmployeController extends Controller
{

    /**
     * Lists all Employe entities.
     *
     * @Route("/getEmploye", name="api_employe_getEmploye")
     * @Method("POST")
     */
    public function getEmployeAction(){

        $em = $this->getDoctrine()->getManager();

        $employe = $em->getRepository('EmployeBundle:Employe')
            ->findOneBy(array('nomComplet' => $_POST['employe']));

        $tab_employe = array(
            'resteConge' => $employe->getComptePresence()->getResteConge(),
            'salaire' => $employe->getSalaire(),
            'parJour' => $employe->getSalaire() / 30,
        );

        return new Response(json_encode($tab_employe));
    }



}
<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('default/index.html.twig', array(
            'base_dir' => realpath($this->container->getParameter('kernel.root_dir').'/..'),
        ));
    }

    private function special_round($number, $precision) {
        $divise = explode('.',($number / $precision));
        $intVal=$divise[0];

        return $intVal*$precision;
    }

    /**
     * @Route("/test/2", name="test_2")
     */
    public function testAction(Request $request)
    {
        return new Response($this->special_round(12261.65,100));
    }
}

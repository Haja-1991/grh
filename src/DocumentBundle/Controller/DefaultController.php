<?php

namespace DocumentBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    public function indexAction($url)
    {
        return $this->redirect($this->generateUrl('redirect_accueil').'document/'.$url);
    }

    public function effacerAction()
    {
        $request = $this->get('request');

        if($request->getMethod() == 'POST'){

            $em = $this->getDoctrine()->getManager();

            $repository_document = $em->getRepository('DocumentBundle:Document');

            $document = $repository_document->findOneBy(array(
                'id' => $_POST['id_document']
            ));

            $em->remove($document);
            $em->flush();

            return $this->redirect($_POST['mon_url']);
        }

        return new Response('Erreur 404');
    }
}

<?php


namespace PresenceBundle\Services;


use Doctrine\Common\Persistence\ObjectManager;
use PresenceBundle\Entity\Conge_Permission;

class PresenceSpService
{
    public function nombreCongeMois(ObjectManager $em, $idEmploye, \DateTime $dateDebut, \DateTime $dateFin){
        $respositoryConge = $em->getRepository('PresenceBundle:Conge_Permission');

        $conges = $respositoryConge->findByMois($idEmploye, $dateDebut, $dateFin);

        $nombre = 0;

        foreach ($conges as $conge){
//            $conge = new Conge_Permission(); // ------------------- A COMMENTER ------------------------
            $nombre += $conge->getNombreJour();
        }

       return $nombre;
    }




}
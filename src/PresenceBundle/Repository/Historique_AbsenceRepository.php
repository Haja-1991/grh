<?php

namespace PresenceBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * Historique_AbsenceRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class Historique_AbsenceRepository extends EntityRepository
{
    public function findByAnne($slug, $dateDebut, $dateFin){

        $qb = $this->createQueryBuilder('hist');

        $qb->join('hist.comptePresence', 'comptePresence')
            ->where('comptePresence.slug = :slug')
            ->andWhere('hist.date >= :dateDebut')
            ->andWhere('hist.date <= :dateFin')
            ->setParameter('slug', $slug)
            ->setParameter('dateDebut', $dateDebut)
            ->setParameter('dateFin', $dateFin)
            ->orderBy('hist.date', 'DESC');

        return $qb->getQuery()
            ->getResult();

    }

}
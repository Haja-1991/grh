<?php

namespace PresenceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Historique_Absence
 *
 * @ORM\Table(name="historique__absence")
 * @ORM\Entity(repositoryClass="PresenceBundle\Repository\Historique_AbsenceRepository")
 */
class Historique_Absence
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetimetz")
     */
    private $date;

    //--------------RELATION--------------------
    /**

     * @ORM\OneToOne(targetEntity="PaieBundle\Entity\Historique_Paie",
    cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $historique_paie_abs;
    /**
     * @ORM\ManyToOne(targetEntity="PresenceBundle\Entity\Compte_Presence",
    cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     *
     */
    private $comptePresence;

    /**
     * @ORM\OneToOne(targetEntity="PresenceBundle\Entity\Conge_Permission",
    cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $congePermission;

    /**
     * @ORM\OneToOne(targetEntity="PresenceBundle\Entity\Retard",
    cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $retard;

    /**
     * @ORM\OneToOne(targetEntity="PresenceBundle\Entity\Absence",
    cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $absence;

    /**
     * @ORM\OneToOne(targetEntity="PaieBundle\Entity\DemandePaiementConge",
    cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $demandePaimenentConge;

    //--------------/////RELATION/////--------------------


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return Historique_Absence
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set comptePresence
     *
     * @param \PresenceBundle\Entity\Compte_Presence $comptePresence
     * @return Historique_Absence
     */
    public function setComptePresence(\PresenceBundle\Entity\Compte_Presence $comptePresence)
    {
        $this->comptePresence = $comptePresence;

        return $this;
    }

    /**
     * Get comptePresence
     *
     * @return \PresenceBundle\Entity\Compte_Presence 
     */
    public function getComptePresence()
    {
        return $this->comptePresence;
    }

    /**
     * Set congePermission
     *
     * @param \PresenceBundle\Entity\Conge_Permission $congePermission
     * @return Historique_Absence
     */
    public function setCongePermission(\PresenceBundle\Entity\Conge_Permission $congePermission = null)
    {
        $this->congePermission = $congePermission;

        return $this;
    }

    /**
     * Get congePermission
     *
     * @return \PresenceBundle\Entity\Conge_Permission 
     */
    public function getCongePermission()
    {
        return $this->congePermission;
    }

    /**
     * Set retard
     *
     * @param \PresenceBundle\Entity\Retard $retard
     * @return Historique_Absence
     */
    public function setRetard(\PresenceBundle\Entity\Retard $retard = null)
    {
        $this->retard = $retard;

        return $this;
    }

    /**
     * Get retard
     *
     * @return \PresenceBundle\Entity\Retard 
     */
    public function getRetard()
    {
        return $this->retard;
    }

    /**
     * Set absence
     *
     * @param \PresenceBundle\Entity\Absence $absence
     * @return Historique_Absence
     */
    public function setAbsence(\PresenceBundle\Entity\Absence $absence = null)
    {
        $this->absence = $absence;

        return $this;
    }

    /**
     * Get absence
     *
     * @return \PresenceBundle\Entity\Absence 
     */
    public function getAbsence()
    {
        return $this->absence;
    }

    /**
     * Set demandePaimenentConge
     *
     * @param \PaieBundle\Entity\DemandePaiementConge $demandePaimenentConge
     * @return Historique_Absence
     */
    public function setDemandePaimenentConge(\PaieBundle\Entity\DemandePaiementConge $demandePaimenentConge = null)
    {
        $this->demandePaimenentConge = $demandePaimenentConge;

        return $this;
    }

    /**
     * Get demandePaimenentConge
     *
     * @return \PaieBundle\Entity\DemandePaiementConge 
     */
    public function getDemandePaimenentConge()
    {
        return $this->demandePaimenentConge;
    }

    /**
     * Set historique_paie_abs
     *
     * @param \PaieBundle\Entity\Historique_Paie $historiquePaieAbs
     * @return Historique_Absence
     */
    public function setHistoriquePaieAbs(\PaieBundle\Entity\Historique_Paie $historiquePaieAbs = null)
    {
        $this->historique_paie_abs = $historiquePaieAbs;

        return $this;
    }

    /**
     * Get historique_paie_abs
     *
     * @return \PaieBundle\Entity\Historique_Paie 
     */
    public function getHistoriquePaieAbs()
    {
        return $this->historique_paie_abs;
    }
}

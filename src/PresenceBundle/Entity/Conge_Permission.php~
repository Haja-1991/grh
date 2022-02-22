<?php

namespace PresenceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Conge_Permission
 *
 * @ORM\Table(name="conge__permission")
 * @ORM\Entity(repositoryClass="PresenceBundle\Repository\Conge_PermissionRepository")
 */
class Conge_Permission
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
     * @ORM\Column(name="dateDebut", type="datetimetz")
     */
    private $dateDebut;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateFin", type="datetimetz")
     */
    private $dateFin;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateRetour", type="datetimetz")
     */
    private $dateRetour;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateDemande", type="datetimetz")
     */
    private $dateDemande;

    /**
     * @var float
     *
     * @ORM\Column(name="nombreJour", type="float")
     */
    private $nombreJour;

    /**
     * @var string
     *
     * @ORM\Column(name="motif", type="text")
     */
    private $motif;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=50)
     */
    private $type;

    //--------------RELATION--------------------

    /**
     * @ORM\OneToOne(targetEntity="PresenceBundle\Entity\Demande_CP",
    cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $demandeCP;

    /**
     * @ORM\ManyToOne(targetEntity="EmployeBundle\Entity\Employe",
    cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     *
     */
    private $employe;

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
     * Set dateDebut
     *
     * @param \DateTime $dateDebut
     * @return Conge_Permission
     */
    public function setDateDebut($dateDebut)
    {
        $this->dateDebut = $dateDebut;

        return $this;
    }

    /**
     * Get dateDebut
     *
     * @return \DateTime 
     */
    public function getDateDebut()
    {
        return $this->dateDebut;
    }

    /**
     * Set dateFin
     *
     * @param \DateTime $dateFin
     * @return Conge_Permission
     */
    public function setDateFin($dateFin)
    {
        $this->dateFin = $dateFin;

        return $this;
    }

    /**
     * Get dateFin
     *
     * @return \DateTime 
     */
    public function getDateFin()
    {
        return $this->dateFin;
    }

    /**
     * Set dateRetour
     *
     * @param \DateTime $dateRetour
     * @return Conge_Permission
     */
    public function setDateRetour($dateRetour)
    {
        $this->dateRetour = $dateRetour;

        return $this;
    }

    /**
     * Get dateRetour
     *
     * @return \DateTime 
     */
    public function getDateRetour()
    {
        return $this->dateRetour;
    }

    /**
     * Set dateDemande
     *
     * @param \DateTime $dateDemande
     * @return Conge_Permission
     */
    public function setDateDemande($dateDemande)
    {
        $this->dateDemande = $dateDemande;

        return $this;
    }

    /**
     * Get dateDemande
     *
     * @return \DateTime 
     */
    public function getDateDemande()
    {
        return $this->dateDemande;
    }

    /**
     * Set nombreJour
     *
     * @param float $nombreJour
     * @return Conge_Permission
     */
    public function setNombreJour($nombreJour)
    {
        $this->nombreJour = $nombreJour;

        return $this;
    }

    /**
     * Get nombreJour
     *
     * @return float 
     */
    public function getNombreJour()
    {
        return $this->nombreJour;
    }

    /**
     * Set motif
     *
     * @param string $motif
     * @return Conge_Permission
     */
    public function setMotif($motif)
    {
        $this->motif = $motif;

        return $this;
    }

    /**
     * Get motif
     *
     * @return string 
     */
    public function getMotif()
    {
        return $this->motif;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Conge_Permission
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set demandeCP
     *
     * @param \PresenceBundle\Entity\Demande_CP $demandeCP
     * @return Conge_Permission
     */
    public function setDemandeCP(\PresenceBundle\Entity\Demande_CP $demandeCP)
    {
        $this->demandeCP = $demandeCP;

        return $this;
    }

    /**
     * Get demandeCP
     *
     * @return \PresenceBundle\Entity\Demande_CP 
     */
    public function getDemandeCP()
    {
        return $this->demandeCP;
    }

    /**
     * Set employe
     *
     * @param \EmployeBundle\Entity\Employe $employe
     * @return Conge_Permission
     */
    public function setEmploye(\EmployeBundle\Entity\Employe $employe)
    {
        $this->employe = $employe;

        return $this;
    }

    /**
     * Get employe
     *
     * @return \EmployeBundle\Entity\Employe 
     */
    public function getEmploye()
    {
        return $this->employe;
    }
}

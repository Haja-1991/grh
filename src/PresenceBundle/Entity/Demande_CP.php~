<?php

namespace PresenceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Demande_CP
 *
 * @ORM\Table(name="demande__c_p")
 * @ORM\Entity(repositoryClass="PresenceBundle\Repository\Demande_CPRepository")
 */
class Demande_CP
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

    /**
     * @var string
     *
     * @ORM\Column(name="etat", type="string", length=50)
     */
    private $etat;

    /**
     * @var string
     *
     * @ORM\Column(name="texte_refuser", type="text", nullable=true)
     */
    private $texte_refuser;

    //--------------RELATION--------------------

    /**
     * @ORM\ManyToOne(targetEntity="EmployeBundle\Entity\Employe",
    cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     *
     */
    private $employe;

    /**
     * @ORM\ManyToOne(targetEntity="MPTDN\UserBundle\Entity\User",
    cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     *
     */
    private $userCreer;

    /**
     * @ORM\ManyToOne(targetEntity="MPTDN\UserBundle\Entity\User",
    cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     *
     */
    private $userConfirme;

    /**
     * @ORM\ManyToOne(targetEntity="MPTDN\UserBundle\Entity\User",
    cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     *
     */
    private $userRefuser;


    //--------------/////RELATION/////--------------------


    public function __construct()
    {
        $this->etat = 'En attente de confirmation';
    }

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
     * @return Demande_CP
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
     * @return Demande_CP
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
     * @return Demande_CP
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
     * Set nombreJour
     *
     * @param float $nombreJour
     * @return Demande_CP
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
     * @return Demande_CP
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
     * @return Demande_CP
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
     * Set dateDemande
     *
     * @param \DateTime $dateDemande
     * @return Demande_CP
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
     * Set employe
     *
     * @param \EmployeBundle\Entity\Employe $employe
     * @return Demande_CP
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

    /**
     * Set userCreer
     *
     * @param \MPTDN\UserBundle\Entity\User $userCreer
     * @return Demande_CP
     */
    public function setUserCreer(\MPTDN\UserBundle\Entity\User $userCreer)
    {
        $this->userCreer = $userCreer;

        return $this;
    }

    /**
     * Get userCreer
     *
     * @return \MPTDN\UserBundle\Entity\User 
     */
    public function getUserCreer()
    {
        return $this->userCreer;
    }

    /**
     * Set userConfirme
     *
     * @param \MPTDN\UserBundle\Entity\User $userConfirme
     * @return Demande_CP
     */
    public function setUserConfirme(\MPTDN\UserBundle\Entity\User $userConfirme = null)
    {
        $this->userConfirme = $userConfirme;

        return $this;
    }

    /**
     * Get userConfirme
     *
     * @return \MPTDN\UserBundle\Entity\User 
     */
    public function getUserConfirme()
    {
        return $this->userConfirme;
    }

    /**
     * Set etat
     *
     * @param string $etat
     * @return Demande_CP
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get etat
     *
     * @return string 
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * Set texte_refuser
     *
     * @param string $texteRefuser
     * @return Demande_CP
     */
    public function setTexteRefuser($texteRefuser)
    {
        $this->texte_refuser = $texteRefuser;

        return $this;
    }

    /**
     * Get texte_refuser
     *
     * @return string 
     */
    public function getTexteRefuser()
    {
        return $this->texte_refuser;
    }

    /**
     * Set userRefuser
     *
     * @param \MPTDN\UserBundle\Entity\User $userRefuser
     * @return Demande_CP
     */
    public function setUserRefuser(\MPTDN\UserBundle\Entity\User $userRefuser = null)
    {
        $this->userRefuser = $userRefuser;

        return $this;
    }

    /**
     * Get userRefuser
     *
     * @return \MPTDN\UserBundle\Entity\User 
     */
    public function getUserRefuser()
    {
        return $this->userRefuser;
    }
}

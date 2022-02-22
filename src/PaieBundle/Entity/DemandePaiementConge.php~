<?php

namespace PaieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DemandePaiementConge
 *
 * @ORM\Table(name="demande_paiement_conge")
 * @ORM\Entity(repositoryClass="PaieBundle\Repository\DemandePaiementCongeRepository")
 */
class DemandePaiementConge
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
     * @ORM\Column(name="etat", type="string", length=50)
     */
    private $etat;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetimetz")
     */
    private $date;

    /**
     * @var float
     *
     * @ORM\Column(name="montant", type="float")
     */
    private $montant;

    //------------RELATION----------

    /**
     * @ORM\ManyToOne(targetEntity="EmployeBundle\Entity\Employe",
    cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     *
     */
    private $empoye;

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
    private $userConfirme1;

    /**
     * @ORM\ManyToOne(targetEntity="MPTDN\UserBundle\Entity\User",
    cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     *
     */
    private $userConfirme2;

    /**
     * @ORM\ManyToOne(targetEntity="MPTDN\UserBundle\Entity\User",
    cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     *
     */
    private $userRefuser;

    /**
     * @ORM\ManyToOne(targetEntity="MPTDN\UserBundle\Entity\User",
    cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     *
     */
    private $userCompta;

    /**
     *
     * API AVY ANY AMLE API COMPTA
     *
     * @var string
     *
     * @ORM\Column(name="nom_caisse", type="string", length=255, nullable=true)
     */
    private $nom_caisse;


    //------------/////////RELATION/////////----------


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
     * Set nombreJour
     *
     * @param float $nombreJour
     * @return DemandePaiementConge
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
     * @return DemandePaiementConge
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
     * Set etat
     *
     * @param string $etat
     * @return DemandePaiementConge
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
     * Set nom_caisse
     *
     * @param string $nomCaisse
     * @return DemandePaiementConge
     */
    public function setNomCaisse($nomCaisse)
    {
        $this->nom_caisse = $nomCaisse;

        return $this;
    }

    /**
     * Get nom_caisse
     *
     * @return string 
     */
    public function getNomCaisse()
    {
        return $this->nom_caisse;
    }

    /**
     * Set empoye
     *
     * @param \EmployeBundle\Entity\Employe $empoye
     * @return DemandePaiementConge
     */
    public function setEmpoye(\EmployeBundle\Entity\Employe $empoye)
    {
        $this->empoye = $empoye;

        return $this;
    }

    /**
     * Get empoye
     *
     * @return \EmployeBundle\Entity\Employe 
     */
    public function getEmpoye()
    {
        return $this->empoye;
    }

    /**
     * Set userCreer
     *
     * @param \MPTDN\UserBundle\Entity\User $userCreer
     * @return DemandePaiementConge
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
     * Set userConfirme1
     *
     * @param \MPTDN\UserBundle\Entity\User $userConfirme1
     * @return DemandePaiementConge
     */
    public function setUserConfirme1(\MPTDN\UserBundle\Entity\User $userConfirme1 = null)
    {
        $this->userConfirme1 = $userConfirme1;

        return $this;
    }

    /**
     * Get userConfirme1
     *
     * @return \MPTDN\UserBundle\Entity\User 
     */
    public function getUserConfirme1()
    {
        return $this->userConfirme1;
    }

    /**
     * Set userConfirme2
     *
     * @param \MPTDN\UserBundle\Entity\User $userConfirme2
     * @return DemandePaiementConge
     */
    public function setUserConfirme2(\MPTDN\UserBundle\Entity\User $userConfirme2 = null)
    {
        $this->userConfirme2 = $userConfirme2;

        return $this;
    }

    /**
     * Get userConfirme2
     *
     * @return \MPTDN\UserBundle\Entity\User 
     */
    public function getUserConfirme2()
    {
        return $this->userConfirme2;
    }

    /**
     * Set userRefuser
     *
     * @param \MPTDN\UserBundle\Entity\User $userRefuser
     * @return DemandePaiementConge
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

    /**
     * Set userCompta
     *
     * @param \MPTDN\UserBundle\Entity\User $userCompta
     * @return DemandePaiementConge
     */
    public function setUserCompta(\MPTDN\UserBundle\Entity\User $userCompta = null)
    {
        $this->userCompta = $userCompta;

        return $this;
    }

    /**
     * Get userCompta
     *
     * @return \MPTDN\UserBundle\Entity\User 
     */
    public function getUserCompta()
    {
        return $this->userCompta;
    }

    /**
     * Set date
     *
     * @param \DateTime $date
     * @return DemandePaiementConge
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
     * Set montant
     *
     * @param float $montant
     * @return DemandePaiementConge
     */
    public function setMontant($montant)
    {
        $this->montant = $montant;

        return $this;
    }

    /**
     * Get montant
     *
     * @return float 
     */
    public function getMontant()
    {
        return $this->montant;
    }
}

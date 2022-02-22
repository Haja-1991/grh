<?php

namespace PaieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Demande_Avance
 *
 * @ORM\Table(name="demande__avance")
 * @ORM\Entity(repositoryClass="PaieBundle\Repository\Demande_AvanceRepository")
 */
class Demande_Avance
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

    /**
     * @var float
     *
     * @ORM\Column(name="montant", type="float")
     */
    private $montant;

    /**
     * @var string
     *
     * @ORM\Column(name="etat", type="string", length=255)
     */
    private $etat;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;


    /**
     * @var boolean
     *
     * @ORM\Column(name="modifiable", type="boolean")
     */
    private $modifiable;


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

    /**
     * @var string
     *
     * @ORM\Column(name="numero", type="string", length=255)
     */
    private $numero;


    //------------/////////RELATION/////////----------


    public function __construct()
    {
        $this->etat = 'En attente de confirmation';
        $this->modifiable = true;
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
     * Set date
     *
     * @param \DateTime $date
     * @return Demande_Avance
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
     * @return Demande_Avance
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

    /**
     * Set empoye
     *
     * @param \EmployeBundle\Entity\Employe $empoye
     * @return Demande_Avance
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
     * @return Demande_Avance
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
     * @return Demande_Avance
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
     * @return Demande_Avance
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
     * Set userCompta
     *
     * @param \MPTDN\UserBundle\Entity\User $userCompta
     * @return Demande_Avance
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
     * Set etat
     *
     * @param string $etat
     * @return Demande_Avance
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
     * Set description
     *
     * @param string $description
     * @return Demande_Avance
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set nom_caisse
     *
     * @param string $nomCaisse
     * @return Demande_Avance
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
     * Set modifiable
     *
     * @param boolean $modifiable
     * @return Demande_Avance
     */
    public function setModifiable($modifiable)
    {
        $this->modifiable = $modifiable;

        return $this;
    }

    /**
     * Get modifiable
     *
     * @return boolean 
     */
    public function getModifiable()
    {
        return $this->modifiable;
    }

    /**
     * Set numero
     *
     * @param integer $numero
     * @return Demande_Avance
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return integer 
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set userRefuser
     *
     * @param \MPTDN\UserBundle\Entity\User $userRefuser
     * @return Demande_Avance
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

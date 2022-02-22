<?php

namespace PaieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Avance_Special
 *
 * @ORM\Table(name="avance__special")
 * @ORM\Entity(repositoryClass="PaieBundle\Repository\Avance_SpecialRepository")
 */
class Avance_Special
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
     * @var float
     *
     * @ORM\Column(name="par_mois", type="float")
     */
    private $parMois;

    /**
     * @var float
     *
     * @ORM\Column(name="somme_paier", type="float")
     */
    private $sommePaier;

    /**
     * @var boolean
     *
     * @ORM\Column(name="etat", type="boolean")
     */
    private $etat;

    /**
     * @var boolean
     *
     * @ORM\Column(name="demander", type="boolean", nullable=true)
     */
    private $demander;

    /**
     * @var string
     *
     * @ORM\Column(name="numero", type="string", length=255, nullable=true)
     */
    private $numero;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="etat_demande", type="string", length=255, nullable=true)
     */
    private $etatDemande;




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
     * @return Avance_Special
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
     * @return Avance_Special
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
     * Set parMois
     *
     * @param float $parMois
     * @return Avance_Special
     */
    public function setParMois($parMois)
    {
        $this->parMois = $parMois;

        return $this;
    }

    /**
     * Get parMois
     *
     * @return float 
     */
    public function getParMois()
    {
        return $this->parMois;
    }

    /**
     * Set sommePaier
     *
     * @param float $sommePaier
     * @return Avance_Special
     */
    public function setSommePaier($sommePaier)
    {
        $this->sommePaier = $sommePaier;

        return $this;
    }

    /**
     * Get sommePaier
     *
     * @return float 
     */
    public function getSommePaier()
    {
        return $this->sommePaier;
    }

    /**
     * Set etat
     *
     * @param boolean $etat
     * @return Avance_Special
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get etat
     *
     * @return boolean 
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * Set empoye
     *
     * @param \EmployeBundle\Entity\Employe $empoye
     * @return Avance_Special
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
     * @return Avance_Special
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
     * Set demander
     *
     * @param boolean $demander
     * @return Avance_Special
     */
    public function setDemander($demander)
    {
        $this->demander = $demander;

        return $this;
    }

    /**
     * Get demander
     *
     * @return boolean 
     */
    public function getDemander()
    {
        return $this->demander;
    }

    /**
     * Set numero
     *
     * @param string $numero
     * @return Avance_Special
     */
    public function setNumero($numero)
    {
        $this->numero = $numero;

        return $this;
    }

    /**
     * Get numero
     *
     * @return string 
     */
    public function getNumero()
    {
        return $this->numero;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return Avance_Special
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
     * @return Avance_Special
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
     * Set userConfirme1
     *
     * @param \MPTDN\UserBundle\Entity\User $userConfirme1
     * @return Avance_Special
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
     * @return Avance_Special
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
     * @return Avance_Special
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
     * @return Avance_Special
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
     * Set etatDemande
     *
     * @param string $etatDemande
     * @return Avance_Special
     */
    public function setEtatDemande($etatDemande)
    {
        $this->etatDemande = $etatDemande;

        return $this;
    }

    /**
     * Get etatDemande
     *
     * @return string 
     */
    public function getEtatDemande()
    {
        return $this->etatDemande;
    }
}

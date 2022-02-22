<?php

namespace PresenceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Absence
 *
 * @ORM\Table(name="absence")
 * @ORM\Entity(repositoryClass="PresenceBundle\Repository\AbsenceRepository")
 */
class Absence
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
     * @ORM\Column(name="dateRetour", type="datetimetz")
     */
    private $dateRetour;

    /**
     * @var float
     *
     * @ORM\Column(name="nombreJour", type="float")
     */
    private $nombreJour;

    /**
     * @var string
     *
     * @ORM\Column(name="etat", type="string", length=50)
     */
    private $etat;

    /**
     * @var string
     *
     * @ORM\Column(name="motif", type="text", nullable=true)
     */
    private $motif;

    /**
     * @var string
     *
     * @ORM\Column(name="texte_refuser", type="text", nullable=true)
     */
    private $texte_refuser;

    /**
     * @var integer
     *
     * @ORM\Column(name="id_hpaie", type="integer", nullable=true)
     */
    private $idHPaie;

    //--------------RELATION--------------------

    /**
     * @ORM\ManyToOne(targetEntity="EmployeBundle\Entity\Employe",
    cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     *
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
        $this->etat = 'Absence signalée';
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
     * @return Absence
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
     * Set dateRetour
     *
     * @param \DateTime $dateRetour
     * @return Absence
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
     * @return Absence
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
     * Set etat
     *
     * @param string $etat
     * @return Absence
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
     * Set employe
     *
     * @param \EmployeBundle\Entity\Employe $employe
     * @return Absence
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
     * Set motif
     *
     * @param string $motif
     * @return Absence
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
     * Set userCreer
     *
     * @param \MPTDN\UserBundle\Entity\User $userCreer
     * @return Absence
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
     * @return Absence
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
     * Set userRefuser
     *
     * @param \MPTDN\UserBundle\Entity\User $userRefuser
     * @return Absence
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
     * Set texte_refuser
     *
     * @param string $texteRefuser
     * @return Absence
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
     * Set idHPaie
     *
     * @param integer $idHPaie
     * @return Absence
     */
    public function setIdHPaie($idHPaie)
    {
        $this->idHPaie = $idHPaie;

        return $this;
    }

    /**
     * Get idHPaie
     *
     * @return integer 
     */
    public function getIdHPaie()
    {
        return $this->idHPaie;
    }
}

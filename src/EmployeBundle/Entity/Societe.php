<?php

namespace EmployeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Societe
 *
 * @ORM\Table(name="societe")
 * @ORM\Entity(repositoryClass="EmployeBundle\Repository\SocieteRepository")
 */
class Societe
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
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=50, unique=true)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse", type="string", length=50)
     */
    private $adresse;

    /**
     * @var string
     *
     * @ORM\Column(name="contact", type="string", length=255)
     */
    private $contact;

    /**
     * @var string
     *
     * @ORM\Column(name="nif", type="string", length=50)
     */
    private $nif;

    /**
     * @var string
     *
     * @ORM\Column(name="stat", type="string", length=255)
     */
    private $stat;

    /**
     * @var string
     *
     * @ORM\Column(name="raison_social", type="string", length=255, nullable=true)
     */
    private $raisonSocial;

    /**
     * @var string
     *
     * @ORM\Column(name="activite", type="string", length=255, nullable=true)
     */
    private $activite;

    /**
     * @var string
     *
     * @ORM\Column(name="adresse2", type="string", length=255, nullable=true)
     */
    private $adresse2;

    /**
     * @var bool
     *
     * @ORM\Column(name="societe_mere", type="boolean", nullable=true)
     */
    private $societeMere;

    /**
     * @var string
     *
     * @ORM\Column(name="abreviation", type="string", length=20, nullable=true)
     */
    private $abreviation;

    /**
     * @var string
     *
     * @ORM\Column(name="compte_banquaire", type="string", length=50, nullable=true)
     */
    private $compteBanquaire;

    /**
     * @var string
     *
     * @ORM\Column(name="banque", type="string", length=50, nullable=true)
     */
    private $banque;

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
     * Set nom
     *
     * @param string $nom
     * @return Societe
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string 
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set adresse
     *
     * @param string $adresse
     * @return Societe
     */
    public function setAdresse($adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get adresse
     *
     * @return string 
     */
    public function getAdresse()
    {
        return $this->adresse;
    }

    /**
     * Set contact
     *
     * @param string $contact
     * @return Societe
     */
    public function setContact($contact)
    {
        $this->contact = $contact;

        return $this;
    }

    /**
     * Get contact
     *
     * @return string 
     */
    public function getContact()
    {
        return $this->contact;
    }

    /**
     * Set nif
     *
     * @param string $nif
     * @return Societe
     */
    public function setNif($nif)
    {
        $this->nif = $nif;

        return $this;
    }

    /**
     * Get nif
     *
     * @return string 
     */
    public function getNif()
    {
        return $this->nif;
    }

    /**
     * Set stat
     *
     * @param string $stat
     * @return Societe
     */
    public function setStat($stat)
    {
        $this->stat = $stat;

        return $this;
    }

    /**
     * Get stat
     *
     * @return string 
     */
    public function getStat()
    {
        return $this->stat;
    }

    /**
     * Set raisonSocial
     *
     * @param string $raisonSocial
     * @return Societe
     */
    public function setRaisonSocial($raisonSocial)
    {
        $this->raisonSocial = $raisonSocial;

        return $this;
    }

    /**
     * Get raisonSocial
     *
     * @return string 
     */
    public function getRaisonSocial()
    {
        return $this->raisonSocial;
    }

    /**
     * Set activite
     *
     * @param string $activite
     * @return Societe
     */
    public function setActivite($activite)
    {
        $this->activite = $activite;

        return $this;
    }

    /**
     * Get activite
     *
     * @return string 
     */
    public function getActivite()
    {
        return $this->activite;
    }

    /**
     * Set adresse2
     *
     * @param string $adresse2
     * @return Societe
     */
    public function setAdresse2($adresse2)
    {
        $this->adresse2 = $adresse2;

        return $this;
    }

    /**
     * Get adresse2
     *
     * @return string 
     */
    public function getAdresse2()
    {
        return $this->adresse2;
    }

    /**
     * Set societeMere
     *
     * @param boolean $societeMere
     * @return Societe
     */
    public function setSocieteMere($societeMere)
    {
        $this->societeMere = $societeMere;

        return $this;
    }

    /**
     * Get societeMere
     *
     * @return boolean 
     */
    public function getSocieteMere()
    {
        return $this->societeMere;
    }

    /**
     * Set abreviation
     *
     * @param string $abreviation
     * @return Societe
     */
    public function setAbreviation($abreviation)
    {
        $this->abreviation = $abreviation;

        return $this;
    }

    /**
     * Get abreviation
     *
     * @return string 
     */
    public function getAbreviation()
    {
        return $this->abreviation;
    }

    /**
     * Set compteBanquaire
     *
     * @param string $compteBanquaire
     * @return Societe
     */
    public function setCompteBanquaire($compteBanquaire)
    {
        $this->compteBanquaire = $compteBanquaire;

        return $this;
    }

    /**
     * Get compteBanquaire
     *
     * @return string 
     */
    public function getCompteBanquaire()
    {
        return $this->compteBanquaire;
    }

    /**
     * Set banque
     *
     * @param string $banque
     * @return Societe
     */
    public function setBanque($banque)
    {
        $this->banque = $banque;

        return $this;
    }

    /**
     * Get banque
     *
     * @return string 
     */
    public function getBanque()
    {
        return $this->banque;
    }
}

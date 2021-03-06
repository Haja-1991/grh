<?php

namespace PaieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Historique_Paie
 *
 * @ORM\Table(name="historique__paie")
 * @ORM\Entity(repositoryClass="PaieBundle\Repository\Historique_PaieRepository")
 */
class Historique_Paie
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
     * @var string
     *
     * @ORM\Column(name="libelle", type="string", length=255)
     */
    private $libelle;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", length=255)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(name="tri", type="string", length=50, nullable=true)
     */
    private $tri;

    /**
     * @var float
     *
     * @ORM\Column(name="montant", type="float")
     */
    private $montant;

    /**
     * @var array
     *
     * @ORM\Column(name="modification", type="array", nullable=true)
     */
    private $test_modification;


    /**
     * @var boolean
     *
     * @ORM\Column(name="salaire_base", type="boolean", nullable=true)
     */
    private $salaireBase;

    /**
     * @var boolean
     *
     * @ORM\Column(name="prise_salaire", type="boolean", nullable=true)
     */
    private $priseSalaire;
    /**
     * @var boolean
     *
     * @ORM\Column(name="avanceS", type="boolean", nullable=true)
     */
    private $ButtonModif;

    /**
     * @var boolean
     *
     * @ORM\Column(name="ostie", type="boolean", nullable=true)
     */
    private $ostie;

    /**
     * @var boolean
     *
     * @ORM\Column(name="cnaps", type="boolean", nullable=true)
     */
    private $cnaps;

    /**
     * @var boolean
     *
     * @ORM\Column(name="irsa", type="boolean", nullable=true)
     */
    private $irsa;

    /**
     * @var float
     *
     * @ORM\Column(name="nb_jour_absence", type="float", nullable=true)
     */
    private $nbJourAbsence;

    /**
     * @var float
     *
     * @ORM\Column(name="nb_jour", type="float", nullable=true)
     */
    private $nbJour;

    /**
     * @var float
     *
     * @ORM\Column(name="nb_heure", type="float", nullable=true)
     */
    private $nbHeure;

    /**
     * @var float
     *
     * @ORM\Column(name="majoration", type="float", nullable=true)
     */
    private $majoration;

    /**
     * @var array
     *
     * @ORM\Column(name="tab_heure_supp", type="array", nullable=true)
     */
    private $tabHeureSupp;

    /**
     * @var string
     *
     * @ORM\Column(name="caisse", type="string", length=255, nullable=true)
     */
    private $caisse;

    /**
     * @var boolean
     *
     * @ORM\Column(name="non_ostie", type="boolean", nullable=true)
     */
    private $nonOstie;

    /**
     * @var float
     *
     * @ORM\Column(name="poucent_org_sat_trav", type="float", nullable=true)
     */
    private $poucentOrgSatTrav;

    /**
     * @var float
     *
     * @ORM\Column(name="poucent_org_sat_empl", type="float", nullable=true)
     */
    private $poucentOrgSatEmpl;

    /**
     * @var float
     *
     * @ORM\Column(name="value_org_sat_trav", type="float", nullable=true)
     */
    private $valueOrgSatTrav;

    /**
     * @var float
     *
     * @ORM\Column(name="value_org_sat_empl", type="float", nullable=true)
     */
    private $valueOrgSatEmpl;


    //--------------RELATION--------------------


    /**
     * @ORM\ManyToOne(targetEntity="PaieBundle\Entity\Compte_Paie",
    cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     *
     */
    private $comptePaie;

    /**
     * @ORM\OneToOne(targetEntity="PaieBundle\Entity\Demande_Avance",
    cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $demandeAvance;

    /**
     * @ORM\OneToOne(targetEntity="PaieBundle\Entity\Avance_Special",
    cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $avanceSpecial;

    /**
     * @ORM\OneToOne(targetEntity="PaieBundle\Entity\DemandePaiementConge",
    cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $demandePaimenentConge;

    /**
     * @ORM\OneToOne(targetEntity="PresenceBundle\Entity\Absence",
    cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $absence;

    /**
     * @ORM\OneToOne(targetEntity="PaieBundle\Entity\Verification",
    cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $verification;



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
     * @return Historique_Paie
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
     * Set libelle
     *
     * @param string $libelle
     * @return Historique_Paie
     */
    public function setLibelle($libelle)
    {
        $this->libelle = $libelle;

        return $this;
    }

    /**
     * Get libelle
     *
     * @return string 
     */
    public function getLibelle()
    {
        return $this->libelle;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Historique_Paie
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
     * Set tri
     *
     * @param string $tri
     * @return Historique_Paie
     */
    public function setTri($tri)
    {
        $this->tri = $tri;

        return $this;
    }

    /**
     * Get tri
     *
     * @return string 
     */
    public function getTri()
    {
        return $this->tri;
    }

    /**
     * Set montant
     *
     * @param float $montant
     * @return Historique_Paie
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
     * Set test_modification
     *
     * @param array $testModification
     * @return Historique_Paie
     */
    public function setTestModification($testModification)
    {
        $this->test_modification = $testModification;

        return $this;
    }

    /**
     * Get test_modification
     *
     * @return array 
     */
    public function getTestModification()
    {
        return $this->test_modification;
    }

    /**
     * Set salaireBase
     *
     * @param boolean $salaireBase
     * @return Historique_Paie
     */
    public function setSalaireBase($salaireBase)
    {
        $this->salaireBase = $salaireBase;

        return $this;
    }

    /**
     * Get salaireBase
     *
     * @return boolean 
     */
    public function getSalaireBase()
    {
        return $this->salaireBase;
    }

    /**
     * Set priseSalaire
     *
     * @param boolean $priseSalaire
     * @return Historique_Paie
     */
    public function setPriseSalaire($priseSalaire)
    {
        $this->priseSalaire = $priseSalaire;

        return $this;
    }

    /**
     * Get priseSalaire
     *
     * @return boolean 
     */
    public function getPriseSalaire()
    {
        return $this->priseSalaire;
    }

    /**
     * Set ButtonModif
     *
     * @param boolean $buttonModif
     * @return Historique_Paie
     */
    public function setButtonModif($buttonModif)
    {
        $this->ButtonModif = $buttonModif;

        return $this;
    }

    /**
     * Get ButtonModif
     *
     * @return boolean 
     */
    public function getButtonModif()
    {
        return $this->ButtonModif;
    }

    /**
     * Set ostie
     *
     * @param boolean $ostie
     * @return Historique_Paie
     */
    public function setOstie($ostie)
    {
        $this->ostie = $ostie;

        return $this;
    }

    /**
     * Get ostie
     *
     * @return boolean 
     */
    public function getOstie()
    {
        return $this->ostie;
    }

    /**
     * Set cnaps
     *
     * @param boolean $cnaps
     * @return Historique_Paie
     */
    public function setCnaps($cnaps)
    {
        $this->cnaps = $cnaps;

        return $this;
    }

    /**
     * Get cnaps
     *
     * @return boolean 
     */
    public function getCnaps()
    {
        return $this->cnaps;
    }

    /**
     * Set irsa
     *
     * @param boolean $irsa
     * @return Historique_Paie
     */
    public function setIrsa($irsa)
    {
        $this->irsa = $irsa;

        return $this;
    }

    /**
     * Get irsa
     *
     * @return boolean 
     */
    public function getIrsa()
    {
        return $this->irsa;
    }

    /**
     * Set comptePaie
     *
     * @param \PaieBundle\Entity\Compte_Paie $comptePaie
     * @return Historique_Paie
     */
    public function setComptePaie(\PaieBundle\Entity\Compte_Paie $comptePaie)
    {
        $this->comptePaie = $comptePaie;

        return $this;
    }

    /**
     * Get comptePaie
     *
     * @return \PaieBundle\Entity\Compte_Paie 
     */
    public function getComptePaie()
    {
        return $this->comptePaie;
    }

    /**
     * Set demandeAvance
     *
     * @param \PaieBundle\Entity\Demande_Avance $demandeAvance
     * @return Historique_Paie
     */
    public function setDemandeAvance(\PaieBundle\Entity\Demande_Avance $demandeAvance = null)
    {
        $this->demandeAvance = $demandeAvance;

        return $this;
    }

    /**
     * Get demandeAvance
     *
     * @return \PaieBundle\Entity\Demande_Avance 
     */
    public function getDemandeAvance()
    {
        return $this->demandeAvance;
    }

    /**
     * Set avanceSpecial
     *
     * @param \PaieBundle\Entity\Avance_Special $avanceSpecial
     * @return Historique_Paie
     */
    public function setAvanceSpecial(\PaieBundle\Entity\Avance_Special $avanceSpecial = null)
    {
        $this->avanceSpecial = $avanceSpecial;

        return $this;
    }

    /**
     * Get avanceSpecial
     *
     * @return \PaieBundle\Entity\Avance_Special 
     */
    public function getAvanceSpecial()
    {
        return $this->avanceSpecial;
    }

    /**
     * Set demandePaimenentConge
     *
     * @param \PaieBundle\Entity\DemandePaiementConge $demandePaimenentConge
     * @return Historique_Paie
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
     * Set nbJourAbsence
     *
     * @param float $nbJourAbsence
     * @return Historique_Paie
     */
    public function setNbJourAbsence($nbJourAbsence)
    {
        $this->nbJourAbsence = $nbJourAbsence;

        return $this;
    }

    /**
     * Get nbJourAbsence
     *
     * @return float 
     */
    public function getNbJourAbsence()
    {
        return $this->nbJourAbsence;
    }

    /**
     * Set absence
     *
     * @param \PresenceBundle\Entity\Absence $absence
     * @return Historique_Paie
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
     * Set nbJour
     *
     * @param float $nbJour
     * @return Historique_Paie
     */
    public function setNbJour($nbJour)
    {
        $this->nbJour = $nbJour;

        return $this;
    }

    /**
     * Get nbJour
     *
     * @return float 
     */
    public function getNbJour()
    {
        return $this->nbJour;
    }

    /**
     * Set majoration
     *
     * @param float $majoration
     * @return Historique_Paie
     */
    public function setMajoration($majoration)
    {
        $this->majoration = $majoration;

        return $this;
    }

    /**
     * Get majoration
     *
     * @return float 
     */
    public function getMajoration()
    {
        return $this->majoration;
    }

    /**
     * Set nbHeure
     *
     * @param float $nbHeure
     * @return Historique_Paie
     */
    public function setNbHeure($nbHeure)
    {
        $this->nbHeure = $nbHeure;

        return $this;
    }

    /**
     * Get nbHeure
     *
     * @return float 
     */
    public function getNbHeure()
    {
        return $this->nbHeure;
    }



    /**
     * Set verification
     *
     * @param \PaieBundle\Entity\Verification $verification
     * @return Historique_Paie
     */
    public function setVerification(\PaieBundle\Entity\Verification $verification = null)
    {
        $this->verification = $verification;

        return $this;
    }

    /**
     * Get verification
     *
     * @return \PaieBundle\Entity\Verification 
     */
    public function getVerification()
    {
        return $this->verification;
    }

    /**
     * Set tabHeureSupp
     *
     * @param array $tabHeureSupp
     * @return Historique_Paie
     */
    public function setTabHeureSupp($tabHeureSupp)
    {
        $this->tabHeureSupp = $tabHeureSupp;

        return $this;
    }

    /**
     * Get tabHeureSupp
     *
     * @return array 
     */
    public function getTabHeureSupp()
    {
        return $this->tabHeureSupp;
    }

    /**
     * Set caisse
     *
     * @param string $caisse
     * @return Historique_Paie
     */
    public function setCaisse($caisse)
    {
        $this->caisse = $caisse;

        return $this;
    }

    /**
     * Get caisse
     *
     * @return string 
     */
    public function getCaisse()
    {
        return $this->caisse;
    }

    /**
     * Set nonOstie
     *
     * @param boolean $nonOstie
     * @return Historique_Paie
     */
    public function setNonOstie($nonOstie)
    {
        $this->nonOstie = $nonOstie;

        return $this;
    }

    /**
     * Get nonOstie
     *
     * @return boolean 
     */
    public function getNonOstie()
    {
        return $this->nonOstie;
    }

    /**
     * Set poucentOrgSatTrav
     *
     * @param float $poucentOrgSatTrav
     * @return Historique_Paie
     */
    public function setPoucentOrgSatTrav($poucentOrgSatTrav)
    {
        $this->poucentOrgSatTrav = $poucentOrgSatTrav;

        return $this;
    }

    /**
     * Get poucentOrgSatTrav
     *
     * @return float 
     */
    public function getPoucentOrgSatTrav()
    {
        return $this->poucentOrgSatTrav;
    }

    /**
     * Set poucentOrgSatEmpl
     *
     * @param float $poucentOrgSatEmpl
     * @return Historique_Paie
     */
    public function setPoucentOrgSatEmpl($poucentOrgSatEmpl)
    {
        $this->poucentOrgSatEmpl = $poucentOrgSatEmpl;

        return $this;
    }

    /**
     * Get poucentOrgSatEmpl
     *
     * @return float 
     */
    public function getPoucentOrgSatEmpl()
    {
        return $this->poucentOrgSatEmpl;
    }

    /**
     * Set valueOrgSatTrav
     *
     * @param float $valueOrgSatTrav
     * @return Historique_Paie
     */
    public function setValueOrgSatTrav($valueOrgSatTrav)
    {
        $this->valueOrgSatTrav = $valueOrgSatTrav;

        return $this;
    }

    /**
     * Get valueOrgSatTrav
     *
     * @return float 
     */
    public function getValueOrgSatTrav()
    {
        return $this->valueOrgSatTrav;
    }

    /**
     * Set valueOrgSatEmpl
     *
     * @param float $valueOrgSatEmpl
     * @return Historique_Paie
     */
    public function setValueOrgSatEmpl($valueOrgSatEmpl)
    {
        $this->valueOrgSatEmpl = $valueOrgSatEmpl;

        return $this;
    }

    /**
     * Get valueOrgSatEmpl
     *
     * @return float 
     */
    public function getValueOrgSatEmpl()
    {
        return $this->valueOrgSatEmpl;
    }
}

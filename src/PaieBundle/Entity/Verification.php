<?php

namespace PaieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Verification
 *
 * @ORM\Table(name="verification")
 * @ORM\Entity(repositoryClass="PaieBundle\Repository\VerificationRepository")
 */
class Verification
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
     * @ORM\Column(name="base_mensuel", type="float")
     */
    private $baseMensuel;

    /**
     * @var float
     *
     * @ORM\Column(name="base_j", type="float")
     */
    private $baseJ;

    /**
     * @var float
     *
     * @ORM\Column(name="total_jour_deduit", type="float")
     */
    private $totalJourDeduit;

    /**
     * @var float
     *
     * @ORM\Column(name="allocation_conge", type="float")
     */
    private $allocationConge;

    /**
     * @var float
     *
     * @ORM\Column(name="allocation_conge_j", type="float")
     */
    private $allocationCongeJ;

    /**
     * @var float
     *
     * @ORM\Column(name="conge", type="float", nullable=true)
     */
    private $conge;

    /**
     * @var float
     *
     * @ORM\Column(name="absence", type="float", nullable=true)
     */
    private $absence;

    /**
     * @var float
     *
     * @ORM\Column(name="base_brute", type="float")
     */
    private $baseBrute;

    /**
     * @var float
     *
     * @ORM\Column(name="indamnites_revenus", type="float")
     */
    private $indamnites_revenus;

    /**
     * @var float
     *
     * @ORM\Column(name="heure_supp_exonerer", type="float")
     */
    private $heureSuppExonerer;

    /**
     * @var float
     *
     * @ORM\Column(name="heure_supp_deductible", type="float")
     */
    private $heureSuppDeductible;

    /**
     * @var float
     *
     * @ORM\Column(name="cnaps", type="float", nullable=true)
     */
    private $cnaps;

    /**
     * @var float
     *
     * @ORM\Column(name="ostie", type="float", nullable=true)
     */
    private $ostie;

    /**
     * @var float
     *
     * @ORM\Column(name="brutte_avant_impot", type="float")
     */
    private $brutteAvantImpot;

    /**
     * @var float
     *
     * @ORM\Column(name="irsa", type="float", nullable=true)
     */
    private $irsa;

    /**
     * @var float
     *
     * @ORM\Column(name="deduction_enfant", type="float")
     */
    private $deductionEnfant;

    /**
     * @var float
     *
     * @ORM\Column(name="irsa_net", type="float")
     */
    private $irsaNet;

    /**
     * @var float
     *
     * @ORM\Column(name="salaire_non_retenu", type="float")
     */
    private $salaireNonRetenu;

    /**
     * @var float
     *
     * @ORM\Column(name="avance_qz", type="float", nullable=true)
     */
    private $avanceQz;

    /**
     * @var float
     *
     * @ORM\Column(name="avance_sp", type="float", nullable=true)
     */
    private $avanceSp;

    /**
     * @var float
     *
     * @ORM\Column(name="autre_retenu", type="float", nullable=true)
     */
    private $autreRetenu;

    /**
     * @var float
     *
     * @ORM\Column(name="net_total", type="float", nullable=true)
     */
    private $netTotal;

    /**
     * @var array
     *
     * @ORM\Column(name="tab_heure_supp_exonerer", type="array", nullable=true)
     */
    private $tabHeureSuppExonerer;

    /**
     * @var array
     *
     * @ORM\Column(name="tab_heure_supp_deductible", type="array", nullable=true)
     */
    private $tabHeureSuppDeductible;



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
     * Set baseMensuel
     *
     * @param float $baseMensuel
     * @return Verification
     */
    public function setBaseMensuel($baseMensuel)
    {
        $this->baseMensuel = $baseMensuel;

        return $this;
    }

    /**
     * Get baseMensuel
     *
     * @return float 
     */
    public function getBaseMensuel()
    {
        return $this->baseMensuel;
    }


    /**
     * Set baseJ
     *
     * @param float $baseJ
     * @return Verification
     */
    public function setBaseJ($baseJ)
    {
        $this->baseJ = $baseJ;

        return $this;
    }

    /**
     * Get baseJ
     *
     * @return float 
     */
    public function getBaseJ()
    {
        return $this->baseJ;
    }

    /**
     * Set totalJourDeduit
     *
     * @param float $totalJourDeduit
     * @return Verification
     */
    public function setTotalJourDeduit($totalJourDeduit)
    {
        $this->totalJourDeduit = $totalJourDeduit;

        return $this;
    }

    /**
     * Get totalJourDeduit
     *
     * @return float 
     */
    public function getTotalJourDeduit()
    {
        return $this->totalJourDeduit;
    }

    /**
     * Set allocationConge
     *
     * @param float $allocationConge
     * @return Verification
     */
    public function setAllocationConge($allocationConge)
    {
        $this->allocationConge = $allocationConge;

        return $this;
    }

    /**
     * Get allocationConge
     *
     * @return float 
     */
    public function getAllocationConge()
    {
        return $this->allocationConge;
    }

    /**
     * Set allocationCongeJ
     *
     * @param float $allocationCongeJ
     * @return Verification
     */
    public function setAllocationCongeJ($allocationCongeJ)
    {
        $this->allocationCongeJ = $allocationCongeJ;

        return $this;
    }

    /**
     * Get allocationCongeJ
     *
     * @return float 
     */
    public function getAllocationCongeJ()
    {
        return $this->allocationCongeJ;
    }

    /**
     * Set conge
     *
     * @param float $conge
     * @return Verification
     */
    public function setConge($conge)
    {
        $this->conge = $conge;

        return $this;
    }

    /**
     * Get conge
     *
     * @return float 
     */
    public function getConge()
    {
        return $this->conge;
    }

    /**
     * Set absence
     *
     * @param float $absence
     * @return Verification
     */
    public function setAbsence($absence)
    {
        $this->absence = $absence;

        return $this;
    }

    /**
     * Get absence
     *
     * @return float 
     */
    public function getAbsence()
    {
        return $this->absence;
    }

    /**
     * Set baseBrute
     *
     * @param float $baseBrute
     * @return Verification
     */
    public function setBaseBrute($baseBrute)
    {
        $this->baseBrute = $baseBrute;

        return $this;
    }

    /**
     * Get baseBrute
     *
     * @return float 
     */
    public function getBaseBrute()
    {
        return $this->baseBrute;
    }

    /**
     * Set indamnites_revenus
     *
     * @param float $indamnitesRevenus
     * @return Verification
     */
    public function setIndamnitesRevenus($indamnitesRevenus)
    {
        $this->indamnites_revenus = $indamnitesRevenus;

        return $this;
    }

    /**
     * Get indamnites_revenus
     *
     * @return float 
     */
    public function getIndamnitesRevenus()
    {
        return $this->indamnites_revenus;
    }

    /**
     * Set heureSuppExonerer
     *
     * @param float $heureSuppExonerer
     * @return Verification
     */
    public function setHeureSuppExonerer($heureSuppExonerer)
    {
        $this->heureSuppExonerer = $heureSuppExonerer;

        return $this;
    }

    /**
     * Get heureSuppExonerer
     *
     * @return float 
     */
    public function getHeureSuppExonerer()
    {
        return $this->heureSuppExonerer;
    }

    /**
     * Set heureSuppDeductible
     *
     * @param float $heureSuppDeductible
     * @return Verification
     */
    public function setHeureSuppDeductible($heureSuppDeductible)
    {
        $this->heureSuppDeductible = $heureSuppDeductible;

        return $this;
    }

    /**
     * Get heureSuppDeductible
     *
     * @return float 
     */
    public function getHeureSuppDeductible()
    {
        return $this->heureSuppDeductible;
    }

    /**
     * Set cnaps
     *
     * @param float $cnaps
     * @return Verification
     */
    public function setCnaps($cnaps)
    {
        $this->cnaps = $cnaps;

        return $this;
    }

    /**
     * Get cnaps
     *
     * @return float 
     */
    public function getCnaps()
    {
        return $this->cnaps;
    }

    /**
     * Set ostie
     *
     * @param float $ostie
     * @return Verification
     */
    public function setOstie($ostie)
    {
        $this->ostie = $ostie;

        return $this;
    }

    /**
     * Get ostie
     *
     * @return float 
     */
    public function getOstie()
    {
        return $this->ostie;
    }

    /**
     * Set brutteAvantImpot
     *
     * @param float $brutteAvantImpot
     * @return Verification
     */
    public function setBrutteAvantImpot($brutteAvantImpot)
    {
        $this->brutteAvantImpot = $brutteAvantImpot;

        return $this;
    }

    /**
     * Get brutteAvantImpot
     *
     * @return float 
     */
    public function getBrutteAvantImpot()
    {
        return $this->brutteAvantImpot;
    }

    /**
     * Set irsa
     *
     * @param float $irsa
     * @return Verification
     */
    public function setIrsa($irsa)
    {
        $this->irsa = $irsa;

        return $this;
    }

    /**
     * Get irsa
     *
     * @return float 
     */
    public function getIrsa()
    {
        return $this->irsa;
    }

    /**
     * Set deductionEnfant
     *
     * @param float $deductionEnfant
     * @return Verification
     */
    public function setDeductionEnfant($deductionEnfant)
    {
        $this->deductionEnfant = $deductionEnfant;

        return $this;
    }

    /**
     * Get deductionEnfant
     *
     * @return float 
     */
    public function getDeductionEnfant()
    {
        return $this->deductionEnfant;
    }

    /**
     * Set irsaNet
     *
     * @param float $irsaNet
     * @return Verification
     */
    public function setIrsaNet($irsaNet)
    {
        $this->irsaNet = $irsaNet;

        return $this;
    }

    /**
     * Get irsaNet
     *
     * @return float 
     */
    public function getIrsaNet()
    {
        return $this->irsaNet;
    }

    /**
     * Set salaireNonRetenu
     *
     * @param float $salaireNonRetenu
     * @return Verification
     */
    public function setSalaireNonRetenu($salaireNonRetenu)
    {
        $this->salaireNonRetenu = $salaireNonRetenu;

        return $this;
    }

    /**
     * Get salaireNonRetenu
     *
     * @return float 
     */
    public function getSalaireNonRetenu()
    {
        return $this->salaireNonRetenu;
    }

    /**
     * Set avanceQz
     *
     * @param float $avanceQz
     * @return Verification
     */
    public function setAvanceQz($avanceQz)
    {
        $this->avanceQz = $avanceQz;

        return $this;
    }

    /**
     * Get avanceQz
     *
     * @return float 
     */
    public function getAvanceQz()
    {
        return $this->avanceQz;
    }

    /**
     * Set avanceSp
     *
     * @param float $avanceSp
     * @return Verification
     */
    public function setAvanceSp($avanceSp)
    {
        $this->avanceSp = $avanceSp;

        return $this;
    }

    /**
     * Get avanceSp
     *
     * @return float 
     */
    public function getAvanceSp()
    {
        return $this->avanceSp;
    }

    /**
     * Set autreRetenu
     *
     * @param float $autreRetenu
     * @return Verification
     */
    public function setAutreRetenu($autreRetenu)
    {
        $this->autreRetenu = $autreRetenu;

        return $this;
    }

    /**
     * Get autreRetenu
     *
     * @return float 
     */
    public function getAutreRetenu()
    {
        return $this->autreRetenu;
    }

    /**
     * Set netTotal
     *
     * @param float $netTotal
     * @return Verification
     */
    public function setNetTotal($netTotal)
    {
        $this->netTotal = $netTotal;

        return $this;
    }

    /**
     * Get netTotal
     *
     * @return float 
     */
    public function getNetTotal()
    {
        return $this->netTotal;
    }

    /**
     * Set tabHeureSuppExonerer
     *
     * @param array $tabHeureSuppExonerer
     * @return Verification
     */
    public function setTabHeureSuppExonerer($tabHeureSuppExonerer)
    {
        $this->tabHeureSuppExonerer = $tabHeureSuppExonerer;

        return $this;
    }

    /**
     * Get tabHeureSuppExonerer
     *
     * @return array 
     */
    public function getTabHeureSuppExonerer()
    {
        return $this->tabHeureSuppExonerer;
    }

    /**
     * Set tabHeureSuppDeductible
     *
     * @param array $tabHeureSuppDeductible
     * @return Verification
     */
    public function setTabHeureSuppDeductible($tabHeureSuppDeductible)
    {
        $this->tabHeureSuppDeductible = $tabHeureSuppDeductible;

        return $this;
    }

    /**
     * Get tabHeureSuppDeductible
     *
     * @return array 
     */
    public function getTabHeureSuppDeductible()
    {
        return $this->tabHeureSuppDeductible;
    }
}

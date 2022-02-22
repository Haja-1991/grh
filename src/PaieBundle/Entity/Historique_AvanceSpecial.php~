<?php

namespace PaieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Historique_AvanceSpecial
 *
 * @ORM\Table(name="historique__avance_special")
 * @ORM\Entity(repositoryClass="PaieBundle\Repository\Historique_AvanceSpecialRepository")
 */
class Historique_AvanceSpecial
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
     * @ORM\Column(name="date", type="datetime")
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
     * @ORM\Column(name="libelle", type="string", length=255)
     */
    private $libelle;

    //--------------RELATION--------------------

    /**
     * @ORM\ManyToOne(targetEntity="PaieBundle\Entity\Avance_Special",
    cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     *
     */
    private $avanceSpecial;
    /**
     *
     * @ORM\OneToOne(targetEntity="PaieBundle\Entity\Historique_Paie")
     */
    private $historique_paie;

    //--------------/////RELATION/////--------------------

    public function __construct()
    {
        $this->libelle = 'Remboursement mensuel';
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
     * @return Historique_AvanceSpecial
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
     * @return Historique_AvanceSpecial
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
     * Set avanceSpecial
     *
     * @param \PaieBundle\Entity\Avance_Special $avanceSpecial
     * @return Historique_AvanceSpecial
     */
    public function setAvanceSpecial(\PaieBundle\Entity\Avance_Special $avanceSpecial)
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
     * Set libelle
     *
     * @param string $libelle
     * @return Historique_AvanceSpecial
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
     * Set historique_paie
     *
     * @param \PaieBundle\Entity\Historique_Paie $historiquePaie
     * @return Historique_AvanceSpecial
     */
    public function setHistoriquePaie(\PaieBundle\Entity\Historique_Paie $historiquePaie = null)
    {
        $this->historique_paie = $historiquePaie;

        return $this;
    }

    /**
     * Get historique_paie
     *
     * @return \PaieBundle\Entity\Historique_Paie 
     */
    public function getHistoriquePaie()
    {
        return $this->historique_paie;
    }
}

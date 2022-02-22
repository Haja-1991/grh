<?php

namespace PaieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Historique_Demande_Avance
 *
 * @ORM\Table(name="historique__demande__avance")
 * @ORM\Entity(repositoryClass="PaieBundle\Repository\Historique_Demande_AvanceRepository")
 */
class Historique_Demande_Avance
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
     * @var array
     *
     * @ORM\Column(name="modification", type="array")
     */
    private $modification;


    //--------------RELATION--------------------

    /**
     * @ORM\OneToOne(targetEntity="PaieBundle\Entity\Demande_Avance",
    cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $demandeAvance;

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
     * @return Historique_Demande_Avance
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
     * @return Historique_Demande_Avance
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
     * Set modification
     *
     * @param array $modification
     * @return Historique_Demande_Avance
     */
    public function setModification($modification)
    {
        $this->modification = $modification;

        return $this;
    }

    /**
     * Get modification
     *
     * @return array 
     */
    public function getModification()
    {
        return $this->modification;
    }

    /**
     * Set demandeAvance
     *
     * @param \PaieBundle\Entity\Demande_Avance $demandeAvance
     * @return Historique_Demande_Avance
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
}

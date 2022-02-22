<?php

namespace PresenceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Retard
 *
 * @ORM\Table(name="retard")
 * @ORM\Entity(repositoryClass="PresenceBundle\Repository\RetardRepository")
 */
class Retard
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
     * @ORM\Column(name="dateHeure", type="datetimetz")
     */
    private $dateHeure;

    /**
     * @var string
     *
     * @ORM\Column(name="motif", type="text", nullable=true)
     */
    private $motif;

    //--------------RELATION--------------------

    /**
     * @ORM\ManyToOne(targetEntity="EmployeBundle\Entity\Employe",
    cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     *
     */
    private $employe;

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
     * Set dateHeure
     *
     * @param \DateTime $dateHeure
     * @return Retard
     */
    public function setDateHeure($dateHeure)
    {
        $this->dateHeure = $dateHeure;

        return $this;
    }

    /**
     * Get dateHeure
     *
     * @return \DateTime 
     */
    public function getDateHeure()
    {
        return $this->dateHeure;
    }

    /**
     * Set motif
     *
     * @param string $motif
     * @return Retard
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
     * Set employe
     *
     * @param \EmployeBundle\Entity\Employe $employe
     * @return Retard
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
}

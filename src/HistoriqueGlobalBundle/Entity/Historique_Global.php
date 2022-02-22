<?php

namespace HistoriqueGlobalBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Historique_Global
 *
 * @ORM\Table(name="historique__global")
 * @ORM\Entity(repositoryClass="HistoriqueGlobalBundle\Repository\Historique_GlobalRepository")
 */
class Historique_Global
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
     * @ORM\Column(name="lien", type="string", length=255)
     */
    private $lien;

    /**
     * @var array
     *
     * @ORM\Column(name="modification", type="array", nullable=true)
     */
    private $modification;

    //-------------

    /**
     * @ORM\ManyToOne(targetEntity="MPTDN\UserBundle\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    //--------------


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
     * @return Historique_Global
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
     * @return Historique_Global
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
     * Set lien
     *
     * @param string $lien
     * @return Historique_Global
     */
    public function setLien($lien)
    {
        $this->lien = $lien;

        return $this;
    }

    /**
     * Get lien
     *
     * @return string 
     */
    public function getLien()
    {
        return $this->lien;
    }

    /**
     * Set modification
     *
     * @param array $modification
     * @return Historique_Global
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
     * Set user
     *
     * @param \MPTDN\UserBundle\Entity\User $user
     * @return Historique_Global
     */
    public function setUser(\MPTDN\UserBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \MPTDN\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }
}

<?php

namespace PaieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Mois
 *
 * @ORM\Table(name="mois")
 * @ORM\Entity(repositoryClass="PaieBundle\Repository\MoisRepository")
 */
class Mois
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
     * @ORM\Column(name="moisencours", type="datetimetz")
     */
    private $moisencours;

    /**
     * @var string
     *
     * @ORM\Column(name="nom_config", type="string", length=50, unique=true)
     */
    private $nom_config;


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
     * Set moisencours
     *
     * @param \DateTime $moisencours
     * @return Mois
     */
    public function setMoisencours($moisencours)
    {
        $this->moisencours = $moisencours;

        return $this;
    }

    /**
     * Get moisencours
     *
     * @return \DateTime 
     */
    public function getMoisencours()
    {
        return $this->moisencours;
    }

    /**
     * Set nom_config
     *
     * @param string $nomConfig
     * @return Mois
     */
    public function setNomConfig($nomConfig)
    {
        $this->nom_config = $nomConfig;

        return $this;
    }

    /**
     * Get nom_config
     *
     * @return string 
     */
    public function getNomConfig()
    {
        return $this->nom_config;
    }
}

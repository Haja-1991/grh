<?php

namespace PaieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Numero_demande
 *
 * @ORM\Table(name="numero_demande")
 * @ORM\Entity(repositoryClass="PaieBundle\Repository\Numero_demandeRepository")
 */
class Numero_demande
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
     * @ORM\Column(name="num_demande", type="string", length=255)
     */
    private $numeroDemande;

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
     * Set numeroDemande
     *
     * @param string $numeroDemande
     * @return Numero_demande
     */
    public function setNumeroDemande($numeroDemande)
    {
        $this->numeroDemande = $numeroDemande;

        return $this;
    }

    /**
     * Get numeroDemande
     *
     * @return string 
     */
    public function getNumeroDemande()
    {
        return $this->numeroDemande;
    }

    /**
     * Set nom_config
     *
     * @param string $nomConfig
     * @return Numero_demande
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

<?php

namespace PresenceBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Compte_Presence
 *
 * @ORM\Table(name="compte__presence")
 * @ORM\Entity(repositoryClass="PresenceBundle\Repository\Compte_PresenceRepository")
 */
class Compte_Presence
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
     * @ORM\Column(name="resteConge", type="float")
     */
    private $resteConge;

    /**
     * @var string
     *
     * @ORM\Column(name="slug", type="string", length=255)
     */
    private $slug;


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
     * Set resteConge
     *
     * @param integer $resteConge
     * @return Compte_Presence
     */
    public function setResteConge($resteConge)
    {
        $this->resteConge = $resteConge;

        return $this;
    }

    /**
     * Get resteConge
     *
     * @return integer 
     */
    public function getResteConge()
    {
        return $this->resteConge;
    }

    /**
     * Set slug
     *
     * @param string $slug
     * @return Compte_Presence
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string 
     */
    public function getSlug()
    {
        return $this->slug;
    }
}

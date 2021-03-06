<?php

namespace DocumentBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Document
 *
 * @ORM\Table(name="document")
 * @ORM\Entity(repositoryClass="DocumentBundle\Repository\DocumentRepository")
 */
class Document
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
     * @ORM\Column(name="nom", type="string", length=255)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, unique=true)
     */
    private $url;

    //----------------RELATION-----------------

    /**
     * @ORM\ManyToOne(targetEntity="EmployeBundle\Entity\Employe",
    cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $employe;

    /**
     * @ORM\ManyToOne(targetEntity="PresenceBundle\Entity\Absence",
    cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $absence;

    /**
     * @ORM\ManyToOne(targetEntity="PresenceBundle\Entity\Demande_CP",
    cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $demandeCP;

    //----------------///RELATION///-----------------


//    -------------MY FUNCTION-------------

    private function randomLettre($len) {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $randnb = '';
        for ($i = 0; $i < $len; ++$i)
            $randnb .= substr($chars, (mt_rand() % strlen($chars)), 1);
        return $randnb;
    }

    public function uploadFichier($nom){

        $rand = $this->randomLettre(5);

        $nom_bd = $rand.' - '.'fichier - '.$_FILES[$nom]["name"];

        $resultat = move_uploaded_file($_FILES[$nom]['tmp_name'],'document/'.$nom_bd);

        if ($resultat){
            return $nom_bd;
        }else{
            return false;
        }
    }

//    -------------///MY FUNCTION///-------------


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
     * @return Document
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
     * Set url
     *
     * @param string $url
     * @return Document
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string 
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set employe
     *
     * @param \EmployeBundle\Entity\Employe $employe
     * @return Document
     */
    public function setEmploye(\EmployeBundle\Entity\Employe $employe = null)
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

    /**
     * Set absence
     *
     * @param \PresenceBundle\Entity\Absence $absence
     * @return Document
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
     * Set demandeCP
     *
     * @param \PresenceBundle\Entity\Demande_CP $demandeCP
     * @return Document
     */
    public function setDemandeCP(\PresenceBundle\Entity\Demande_CP $demandeCP = null)
    {
        $this->demandeCP = $demandeCP;

        return $this;
    }

    /**
     * Get demandeCP
     *
     * @return \PresenceBundle\Entity\Demande_CP 
     */
    public function getDemandeCP()
    {
        return $this->demandeCP;
    }
}

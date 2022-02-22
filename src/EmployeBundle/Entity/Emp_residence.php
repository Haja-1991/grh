<?php

namespace EmployeBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Emp_residence
 *
 * @ORM\Table(name="emp_residence")
 * @ORM\Entity(repositoryClass="EmployeBundle\Repository\Emp_residenceRepository")
 */
class Emp_residence
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
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private $url;

    //----------------RELATION-----------------

    /**
     * @ORM\ManyToOne(targetEntity="EmployeBundle\Entity\Employe",
    cascade={"persist"})
     * @ORM\JoinColumn(nullable=true)
     */
    private $employe;


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

        $nom_bd = 'residence\\'.$rand.' - '.'fichier - '.$_FILES[$nom]["name"];

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
     * Set date
     *
     * @param \DateTime $date
     * @return Emp_residence
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
     * Set url
     *
     * @param string $url
     * @return Emp_residence
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
     * @return Emp_residence
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
}

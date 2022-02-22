<?php

namespace MPTDN\UserBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string")
     */
    private $nom;



    public function getMyRoles()
    {
        $ral = $this->getRoles();

        $rol = $ral[0];

        return $rol;
    }

    public function getSiCompta(){
        $role_compta = false;

        foreach($this->getRoles() as $rol){
            if($rol == 'ROLE_COMPTA'){
                $role_compta = true;
            }
        }

        return $role_compta;
    }

    public function ifRole($rl){
        $ok = false;

        foreach($this->getRoles() as $rol){
            if($rol == $rl){
                $ok = true;
            }
        }

        return $ok;
    }


    /**
     * Set nom
     *
     * @param string $nom
     * @return User
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


}

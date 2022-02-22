<?php
/**
 * Created by PhpStorm.
 * User: Allin
 * Date: 12/12/2018
 * Time: 15:33
 */

namespace AppBundle\Twig;

use EmployeBundle\Entity\Employe;
use PresenceBundle\Entity\Retard;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;


class AppExtension extends \Twig_Extension
{

    protected $doctrine;

    public function __construct(RegistryInterface $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    private function special_round($number, $precision) {
        $divise = explode('.',($number / $precision));
        $intVal=$divise[0];

        return $intVal*$precision;
    }

    public function getFilters(){
        return array(
            new \Twig_SimpleFilter('typeFile', array($this, 'getTypeFile')),
            new \Twig_SimpleFilter('abrever', array($this, 'contenuCour')),
            new \Twig_SimpleFilter('retard', array($this, 'intervalleRetard')),
            new \Twig_SimpleFilter('monjour', array($this, 'monJourFunc')),
            new \Twig_SimpleFilter('monmois', array($this, 'monMoisFunc')),
            new \Twig_SimpleFilter('nbrRetard', array($this, 'nbrRetard')),
            new \Twig_SimpleFilter('dateDiff', array($this, 'differenceDate')),
            new \Twig_SimpleFilter('arrondir', array($this, 'arrondirFunc')),
        );
    }

    function monJourFunc($date) {

        $jour_semaine = array(1=>"lundi", 2=>"mardi", 3=>"mercredi", 4=>"jeudi", 5=>"vendredi", 6=>"samedi", 7=>"dimanche");

        list($annee, $mois, $jour) = explode ("-", $date);

        $timestamp = mktime(0,0,0, date($mois), date($jour), date($annee));
        $njour = date("N",$timestamp);

        return $jour_semaine[$njour];
    }

    function monMoisFunc($mois) {

        $mois = intval($mois);

        $array_mois = array("", "Janvier", "Fevrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Aout", "Septembre", "Octobre", "Novembre", "Decembre", "");

        return $array_mois[$mois];

    }

    function contenuCour($text){
        if(strlen($text) < 35){
            return $text;
        }
        $coupe = substr($text, 0, 35);
        $coupe = $coupe.' ...';
        return $coupe;
    }

    function intervalleRetard(\DateTime $dateRetard){

        $dateTest = clone $dateRetard;

        $heure = intval($dateRetard->format('H'));

        if($heure >= 8 and $heure < 14){
            $dateTest->setTime(8, 0);

            $interval = $dateRetard->diff($dateTest);

            if($interval->h == 0) return $interval->i.' min';

            return $interval->h.' h '.$interval->i.' min';

        }else{
            $dateTest->setTime(14, 0);

            $interval = $dateRetard->diff($dateTest);

            if($interval->h == 0) return $interval->i.' min';

            return $interval->h.' h '.$interval->i.' min';
        }
    }

    function dateFR($datefr)
    {
        $Jour = array("Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi","Samedi");
        $Mois = array("Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre");
        $m = (intval(date('m')) - 1);
        $datefr = date("d")." ".$Mois[$m]." ".date("Y");
        return $datefr;
    }

    function getTypeFile($nom){
        $type = 'file';

        $excel = array('xls' ,'xls','xlsx','xls','xlsm','xlt','xltx','xlt','xltm','xla','xlam');
        $audio = array('mp3', 'wma', 'ogg', 'aac');
        $video = array('avi','mp4','mov','flv','mpg');
        $image = array('tif', 'tiff', 'gif', 'jpeg', 'jpg', 'jif', 'jfif', 'jp2', 'jpx', 'j2k', 'j2c', 'fpx', 'pcd', 'png');

        $ext = substr(strrchr($nom,'.'),1);

        if ($ext == 'doc' or $ext == 'docx') $type = 'word';

        if(in_array($ext, $excel)) $type = 'excel';

        if ($ext == 'pdf') $type = 'pdf';

        if(in_array($ext, $audio)) $type = 'audio';

        if(in_array($ext, $video)) $type = 'video';

        if(in_array($ext, $image)) $type = 'image';

        return $type;

    }

    public function nbrRetard(Employe $employe){

        $nbr=0;
        $em=$this->doctrine->getManager();
        $retards=$em->getRepository('PresenceBundle:Retard')->findBy(array('employe'=>$employe));
        foreach ($retards as $retard)
        {
            $nbr=$nbr+1;
        }
        return $nbr;
    }

    public function differenceDate(\DateTime $date1, \DateTime $date2){

        $interval = $date1->diff($date2);
        $mois = $interval->format('%m');
        $annee = $interval->format('%y');
        $jour = $interval->format('%d');

        $diff = intval($mois) + (intval($annee) * 12);

        if($jour > 0){
            $diff ++;
        }

        return $diff;
    }

    public function arrondirFunc($nombre){
        return $this->special_round($nombre, 100);
    }

    public function getName(){
        return 'typefact_extensions';
    }

}
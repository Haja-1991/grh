<?php


namespace PaieBundle\Services;


use Doctrine\Common\Persistence\ObjectManager;
use EmployeBundle\Entity\Employe;
use PaieBundle\Entity\Compte_Paie;
use PaieBundle\Entity\Historique_Paie;
use PaieBundle\Entity\Verification;
use PresenceBundle\Services\PresenceSpService;

class PaieService
{
    public function testIrsa(ObjectManager $em, Employe $employe, $mois, $annee){

        $date_test = mktime( 0, 0, 0, $mois, 1, $annee );
        $fin_mois = date('t', $date_test) + 1;

        $repository_historiquePaie = $em->getRepository('PaieBundle:Historique_Paie');

        $comptePaie = $employe->getComptePaie();

        $dateDebut = \ DateTime::createFromFormat('d/m/Y', '1/'.$mois.'/'.$annee)->format('Y-m-d');
        $dateFin = \ DateTime::createFromFormat('d/m/Y', $fin_mois.'/'.$mois.'/'.$annee)->format('Y-m-d');

        $historique_paies = $repository_historiquePaie->findByMoisAnne($comptePaie->getSlug(), $dateDebut, $dateFin);

        $irsa = false;

        foreach ($historique_paies as $historique_paie){

//            $historique_paie = new Historique_Paie();
            if ($historique_paie->getIrsa())
                $irsa = true;

        }

        return $irsa;

    }

    public function special_round($number, $precision) {
        $divise = explode('.',($number / $precision));
        $intVal=$divise[0];

        return $intVal*$precision;
    }

    private function calculHeure($heureSupps){
        $totalHeure = 0;

        foreach ($heureSupps as $heureSupp){
            $totalHeure = $totalHeure + $heureSupp['heure'];
        }

        return $totalHeure;
    }

    private function montantHeureSupp($salaire_pH, $heureSupps){
        $montant = 0;

        foreach ($heureSupps as $heureSupp){
            $montant = $montant + (($salaire_pH * $heureSupp['heure']) + ($salaire_pH * $heureSupp['heure'] * $heureSupp['majoration'] / 100));
        }

        return $montant;
    }

    public function deductionSalaireEmploye(ObjectManager $em, Compte_Paie $compte_Paie, Employe $employe, \DateTime $dateDebut, \DateTime $dateFin){
        $respositoryHistoriquePaie = $em->getRepository('PaieBundle:Historique_Paie');
        $dateDebut = \DateTime::createFromFormat('d/m/Y h:i', ''.$dateDebut->format('d').'/'.$dateDebut->format('m').'/'.$dateDebut->format('Y').' '."00:00");
        $historiques = $respositoryHistoriquePaie->findByMoisAnne($compte_Paie->getSlug(), $dateDebut, $dateFin);

        $revenus = array();
        $heureSups = array();
        $retenues = array();
        $absences = array();
        $tmp_baseTravailler = null;

        $aEffacers = array();

        $base = $employe->getSalaire();
        $allConge = $employe->getAllocationConge();
        if (! $allConge){
            $allConge = $base;
        }

        $pJ_base = $base / 30;
        $pJ_allConge = $allConge / 30;

        $pH_base = $base / 30 / 8;

        foreach ($historiques as $historiquePaie){
//            $historiquePaie = new Historique_Paie(); //---------------- A COMMENTER ---------------

            if($historiquePaie->getType() == "credit" and !$historiquePaie->getSalaireBase()){
                array_push($revenus, $historiquePaie);
            }

            if($historiquePaie->getType() == "heureSup"){
                array_push($heureSups, $historiquePaie);
            }

            if($historiquePaie->getType() == "debit"){
                array_push($retenues, $historiquePaie);
            }

            if($historiquePaie->getSalaireBase()){
//                array_push($aEffacers, $historiquePaie);
                $tmp_baseTravailler = $historiquePaie;
            }

            if($historiquePaie->getAbsence() and $historiquePaie->getType() == "absence"){
                array_push($absences, $historiquePaie);
            }

        }

        $verification = new Verification();

        // ---- SALAIRE ET ALLOCATION CONGE --------
        $verification->setBaseMensuel($base);
        $verification->setAllocationConge($allConge);
        $verification->setBaseJ($pJ_base);
        $verification->setAllocationCongeJ($pJ_allConge);
        

        //---- NOMBRE CONGE ------
        $servicePresence = new PresenceSpService();
        $nombreConge = $servicePresence->nombreCongeMois($em, $employe->getId(), $dateDebut, $dateFin);
        $verification->setConge($nombreConge);

        //----- ABSENCE ------
        $nombreAbsence = 0;
        foreach ($absences as $absence){
            $nombreAbsence += $absence->getNbJourAbsence();
        }
        $verification->setAbsence($nombreAbsence);

        // ----- CALCUL BASE AVEC ALLOC CONGE ----
        $jourTravailler = 30 - $nombreConge - $nombreAbsence;
        $baseTravailler = $jourTravailler * $pJ_base;
        $baseAlloc = $nombreConge * $pJ_allConge;
        $baseTotal = $baseTravailler + $baseAlloc;

        $verification->setTotalJourDeduit($jourTravailler);

        
        $verification->setBaseBrute($baseTotal);
        
        $baseFinal = new Historique_Paie();
        $baseFinal->setMontant($baseTravailler);
        $baseFinal->setType('base_final');
        $baseFinal->setNbJour($jourTravailler);
        $baseFinal->setComptePaie($compte_Paie);
        $baseFinal->setLibelle("Salaire de base");
        $baseFinal->setDate($dateDebut);
        $baseFinal->setSalaireBase(1);
        $em->persist($baseFinal);
        
        $baseAllocFinal = new Historique_Paie();
        $baseAllocFinal->setMontant($baseAlloc);
        $baseAllocFinal->setNbJour($nombreConge);
        $baseAllocFinal->setType('alloc_final');
        $baseAllocFinal->setComptePaie($compte_Paie);
        $baseAllocFinal->setLibelle('Congé');
        $baseAllocFinal->setDate($dateDebut);
        $em->persist($baseAllocFinal);

        // -----  /////////// CALCUL BASE AVEC ALLOC CONGE ///////////  ----


        // ------------------- CALCUL REVENU & INDAMNITE ---------------------
        
        $totalRevenuIndam = 0;
        foreach ($revenus as $revenu){
//            $revenu = new Historique_Paie();  // - ------ A COMMENTER ------
            $totalRevenuIndam += $revenu->getMontant();
        }
        $verification->setIndamnitesRevenus($totalRevenuIndam);
        // ------------------- ////// CALCUL REVENU & INDAMNITE ////// ---------------------
        
        
        
        // ------------------- CALCUL HEURE SSUPP. ---------------------

        $tabSupps = array(
            array('majoration'=> 30, 'heure' => 0),
            array('majoration'=> 40, 'heure' => 0),
            array('majoration'=> 50, 'heure' => 0),
            array('majoration'=> 100, 'heure' => 0),
        );

        if (count($heureSups) > 0){
            foreach ($heureSups as $heureSup){
//            $heureSup = new Historique_Paie(); //------ A COMMENTER ------
                if($heureSup->getMajoration() == 30){
                    $tabSupps[0]['heure'] +=  $heureSup->getNbHeure();
                }

                if($heureSup->getMajoration() == 40){
                    $tabSupps[1]['heure'] +=  $heureSup->getNbHeure();
                }

                if($heureSup->getMajoration() == 50){
                    $tabSupps[2]['heure'] +=  $heureSup->getNbHeure();
                }

                if($heureSup->getMajoration() == 100){
                    $tabSupps[3]['heure'] +=  $heureSup->getNbHeure();
                }
            }
        }

        $heureExonerers = $tabSupps;

        $heureDeductibles = array(
            array('majoration'=> 30, 'heure' => 0),
            array('majoration'=> 40, 'heure' => 0),
            array('majoration'=> 50, 'heure' => 0),
            array('majoration'=> 100, 'heure' => 0),
        );

        if ($this->calculHeure($tabSupps) > 20){
            $testDemiM3 = round($tabSupps[0]['heure'] / 2);
            $testDemiM4 = round($tabSupps[1]['heure'] / 2);
            $testDemiM5 = round($tabSupps[2]['heure'] / 2);
            $testDemiM10 = round($tabSupps[3]['heure'] / 2);


            $totalHexonerer = $this->calculHeure($heureExonerers);

            while ($totalHexonerer != 20){

                $testModification = 0;

                if (($totalHexonerer == 20)){
                    break;
                }

                if($testDemiM3 < $heureExonerers[0]['heure'] ){
                    $heureExonerers[0]['heure'] = $heureExonerers[0]['heure'] - 1;
                    $heureDeductibles[0]['heure'] = $heureDeductibles[0]['heure'] + 1;
                    $totalHexonerer --;

                    $testModification ++;

                    if (($totalHexonerer == 20)){
                        break;
                    }

                }

                if($testDemiM4 < $heureExonerers[1]['heure'] ){
                    $heureExonerers[1]['heure'] = $heureExonerers[1]['heure'] - 1;
                    $heureDeductibles[1]['heure'] = $heureDeductibles[1]['heure'] + 1;
                    $totalHexonerer --;

                    $testModification ++;

                    if (($totalHexonerer == 20)){
                        break;
                    }

                }

                if($testDemiM5 < $heureExonerers[2]['heure'] ){
                    $heureExonerers[2]['heure'] = $heureExonerers[2]['heure'] - 1;
                    $heureDeductibles[2]['heure'] = $heureDeductibles[2]['heure'] + 1;
                    $totalHexonerer --;

                    $testModification ++;

                    if (($totalHexonerer == 20)){
                        break;
                    }

                }

                if($testDemiM10 < $heureExonerers[3]['heure'] ){
                    $heureExonerers[3]['heure'] = $heureExonerers[3]['heure'] - 1;
                    $heureDeductibles[3]['heure'] = $heureDeductibles[3]['heure'] + 1;
                    $totalHexonerer --;

                    $testModification ++;

                    if (($totalHexonerer == 20)){
                        break;
                    }

                }

                if($testModification == 0){
                    $i = 0;
                    $tmpTest = 0;
                    $tmpHeure = 0;
                    foreach ($heureExonerers as $heureExonerer){
                        if ($heureExonerers[$i]['heure'] > $tmpHeure){
                            $tmpTest = $i;
                            $tmpHeure = $heureExonerers[$i]['heure'];
                        }
                        $i ++;
                    }


                    if ($heureExonerers[$tmpTest]['heure'] > 1){
                        $heureExonerers[$tmpTest]['heure'] = $heureExonerers[$tmpTest]['heure'] - 1;
                        $heureDeductibles[$tmpTest]['heure'] = $heureDeductibles[$tmpTest]['heure'] + 1;

                        $totalHexonerer --;
                    }

                    if (($totalHexonerer == 20)){
                        break;
                    }
                }

            }
        }

        //----VRAI CALCUL---
        $montantExonerer = $this->montantHeureSupp($pH_base, $heureExonerers);
        $montantDeductible = $this->montantHeureSupp($pH_base, $heureDeductibles);

        $verification->setHeureSuppExonerer($montantExonerer);
        $verification->setHeureSuppDeductible($montantDeductible);
        $verification->setTabHeureSuppExonerer($heureExonerers);
        $verification->setTabHeureSuppDeductible($heureDeductibles);
        
        //---SET NEW ACTION CAISSE---
        $final_exonerer = new Historique_Paie();
        $final_exonerer->setComptePaie($compte_Paie);
        $final_exonerer->setLibelle("Heure Supp. Exonerer");
        $final_exonerer->setType('exonerer');
        $final_exonerer->setMontant($montantExonerer);
        $final_exonerer->setDate($dateFin);
        $final_exonerer->setTabHeureSupp($heureExonerers);
        $em->persist($final_exonerer);

        $final_deductible = new Historique_Paie();
        $final_deductible->setComptePaie($compte_Paie);
        $final_deductible->setLibelle("Heure Supp. Déductible");
        $final_deductible->setType('deductible');
        $final_deductible->setMontant($montantDeductible);
        $final_deductible->setTabHeureSupp($heureDeductibles);
        $final_deductible->setDate($dateFin);
        $em->persist($final_deductible);
        
        // ------------------- ////// CALCUL HEURE SSUPP. ////// ---------------------

        // ------------------- CALCUL OSTIE & CNAPS & IRSA ---------------------

        $bruteDuMois = $baseTravailler + $baseAlloc + $totalRevenuIndam + $montantDeductible + $montantExonerer;

        $ostieCnaps = $bruteDuMois * 0.01;


        $ostie = new Historique_Paie();
        $ostie->setType('debit');
        $ostie->setLibelle('Cotisation OSTIE');
        $ostie->setDate($dateFin);
        $ostie->setOstie(true);
        $ostie->setComptePaie($compte_Paie);
        $ostie->setMontant($ostieCnaps);

        if ($employe->getInnactifOstie() == true){
            $ostie->setLibelle('Organisation Sanitaire '.$employe->getPoucentOrgSatTrav().'%');
            $ostie->setNonOstie(true);
            $ostie->setMontant($ostie->getMontant() * $employe->getPoucentOrgSatTrav());
            $ostie->setPoucentOrgSatTrav($employe->getPoucentOrgSatTrav());
            $ostie->setPoucentOrgSatEmpl($employe->getPoucentOrgSatEmpl());
            $ostie->setValueOrgSatEmpl($ostieCnaps * $ostie->getPoucentOrgSatEmpl());
            
        }

        if($ostie->getMontant() > 16000){
            $ostie->setMontant(16000);
        }

        $em->persist($ostie);

        $cnaps = new Historique_Paie();
        $cnaps->setType('debit');
        $cnaps->setDate($dateFin);
        $cnaps->setComptePaie($compte_Paie);
        $cnaps->setMontant(0);
        $cnaps->setCnaps(true);
        $cnaps->setLibelle('Cotisation CNaPS');

        if ($employe->getInnactifCnaps() != true){
            $cnaps->setMontant($ostieCnaps);
        }

        if($cnaps->getMontant() > 16000){
            $cnaps->setMontant(16000);
        }

        $em->persist($cnaps);

        $revenuNet = $bruteDuMois - ($cnaps->getMontant() + $ostie->getMontant()) - $montantExonerer;
        $revenuNet = $this->special_round($revenuNet, 100);
        $montantIrsa = 3000;
        $deductionEnfant = 0;

        if($employe->getNbEnfant() > 0){
            $deductionEnfant = $employe->getNbEnfant() * 2000;
        }

        if ($revenuNet <= 400000 ){
            $montantIrsa = ($revenuNet - 350000) * (5/100);
        }else{
            if ($revenuNet <= 500000){
                $montantIrsa = 2500 + ($revenuNet - 400000) * (10/100);
            }else{
                if($revenuNet <= 600000){
                    $montantIrsa = 12500 + ($revenuNet - 500000) * (15/100);
                }else{
                    if($revenuNet > 600000){
                        $montantIrsa = 27500 + ($revenuNet - 600000) * (20/100);
                    }
                }
            }
        }

        $montantIrsa = $montantIrsa - $deductionEnfant;

        if ($montantIrsa < 3000){
            $montantIrsa = 3000;
            $deductionEnfant = 0;
        }

        $verification->setBrutteAvantImpot($revenuNet);
        $verification->setDeductionEnfant($deductionEnfant);
        $verification->setSalaireNonRetenu($revenuNet);

        $irsa = new Historique_Paie();
        $irsa->setType('debit');
        $irsa->setLibelle('Prélèvement IRSA');
        $irsa->setDate($dateFin);
        $irsa->setIrsa(true);
        $irsa->setComptePaie($compte_Paie);
        $irsa->setMontant($montantIrsa);

        $em->persist($irsa);

        $verification->setIrsaNet($irsa->getMontant());
        $verification->setCnaps($cnaps->getMontant());
        $verification->setOstie($ostie->getMontant());

        // ------------------- ////// CALCUL OSTIE & CNAPS & IRSA ////// ---------------------


        // ------------------- EFFACER TEMP ---------------------

        foreach ($aEffacers as $aEffacer){
            $em->remove($aEffacer);
        }

        $em->remove($tmp_baseTravailler);

        $actionVerification = new Historique_Paie();
        $actionVerification->setLibelle('Vérification');
        $actionVerification->setType('verification');
        $actionVerification->setMontant(0);
        $actionVerification->setVerification($verification);
        $actionVerification->setDate($dateFin);
        $actionVerification->setComptePaie($compte_Paie);

        $em->persist($actionVerification);
        // ------------------- ////// EFFACER SALAIRE ////// ---------------------


        // ------------------- AJOUTER NOMBRE ENFANT ET SALAIRE DE BASE ---------------------

        $tmpSalaireBase = new Historique_Paie();
        $tmpSalaireBase->setType('tmp_base');
        $tmpSalaireBase->setLibelle('Salaire de base');
        $tmpSalaireBase->setDate($dateFin);
        $tmpSalaireBase->setComptePaie($compte_Paie);
        $tmpSalaireBase->setMontant($employe->getSalaire());

        $em->persist($tmpSalaireBase);

        $tmpNombreEnfant = new Historique_Paie();
        $tmpNombreEnfant->setType('tmp_nbEnfant');
        $tmpNombreEnfant->setLibelle('Nombre enfant');
        $tmpNombreEnfant->setDate($dateFin);
        $tmpNombreEnfant->setComptePaie($compte_Paie);
        if($employe->getNbEnfant()){
            $tmpNombreEnfant->setMontant($employe->getNbEnfant());
        }else{
            $tmpNombreEnfant->setMontant(0);
        }

        $em->persist($tmpNombreEnfant);

        $minimumPerception = new Historique_Paie();
        $minimumPerception->setType('tmp_minimumPerception');
        $minimumPerception->setLibelle('Minimum perception');
        $minimumPerception->setDate($dateFin);
        $minimumPerception->setComptePaie($compte_Paie);
        $minimumPerception->setMontant(3000);

        $em->persist($minimumPerception);

        // ------------------- ////// AJOUTER NOMBRE ENFANT ET SALAIRE DE BASE ////// ---------------------

        // ------------------ RESTE CONGE ------------------

        $resteCongeC = new Historique_Paie();
        $resteCongeC->setType('tmp_reste_conge');
        $resteCongeC->setLibelle('Reste Congé');
        $resteCongeC->setDate($dateFin);
        $resteCongeC->setComptePaie($compte_Paie);
        $resteCongeC->setMontant($employe->getComptePresence()->getResteConge());

        $em->persist($resteCongeC);

        // ------------------///// RESTE CONGE /////------------------

    }

}
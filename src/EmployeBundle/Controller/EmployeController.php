<?php

namespace EmployeBundle\Controller;

use DocumentBundle\Entity\Document;
use EmployeBundle\Entity\Emp_cin;
use EmployeBundle\Entity\Emp_residence;
use HistoriqueGlobalBundle\Entity\Historique_Global;
use PaieBundle\Entity\Compte_Paie;
use PresenceBundle\Entity\Compte_Presence;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use EmployeBundle\Entity\Employe;
use EmployeBundle\Form\EmployeType;
use Symfony\Component\HttpFoundation\Response;

/**
 * Employe controller.
 *
 * @Route("/")
 */
class EmployeController extends Controller
{

    //-----------------MES FONCTIONS---------------

    private function randomLettre($len) {
        $chars = 'ABCDEFGHIJK123456789';
        $randnb = '';
        for ($i = 0; $i < $len; ++$i)
            $randnb .= substr($chars, (mt_rand() % strlen($chars)), 1);
        return $randnb;
    }

    private function uploadImageProfil($nom) {
        $rand = $this->randomLettre(5);

        $nom_bd = $rand.' - '.'fichier - '.$_FILES[$nom]["name"];

        $resultat = move_uploaded_file($_FILES[$nom]['tmp_name'],'document/imagesEmployes/'.$nom_bd);

        if ($resultat){
            return $nom_bd;
        }else{
            return false;
        }
    }

    private function moisDeServiceAction(\DateTime $date_debut)
    {

        $dateActuel = new \DateTime();

        if($date_debut > $dateActuel) return new Response('Erreur sur la date de début ou date du système');
        else {
            $intervalle = $dateActuel->diff($date_debut);

            $nombre_mois = 0;

            $annee_int = intval($intervalle->y);
            $mois_int = intval($intervalle->m);
            $day_int = intval($intervalle->d);


            if($annee_int != 0) $nombre_mois = $annee_int * 12;

            $nombre_mois = $nombre_mois + $mois_int;

            if($day_int != 0) $nombre_mois = $nombre_mois + 1;

            return $nombre_mois;
        }
    }

    //-----------------//////MES FONCTIONS//////---------------


    /**
     * Lists all Employe entities.
     *
     * @Route("/dossier/imcomplet", name="employe_test_erreur")
     * @Method("GET")
     */
    public function TestDossierImcopletAction()
    {
        $em = $this->getDoctrine()->getManager();

        $employes = $em->getRepository('EmployeBundle:Employe')->findBy(
            array('etat' => true),
            array('nom' => 'asc')
        );

        $repository_cin = $em->getRepository('EmployeBundle:Emp_cin');
        $repository_residence = $em->getRepository('EmployeBundle:Emp_residence');

        $dateActuel = new \DateTime();

        $tabEmployes = array();
        foreach ($employes as $employe){

            $erreur = array();

            $cin = $repository_cin->findOneBy(array(
               'employe' => $employe
           ));

            $residence = $repository_residence->findOneBy(array(
                'employe' => $employe
            ));


           if(!$cin)
               array_push($erreur, 'CIN');
           else{
               $dateCIN = $cin->getDate();

               $interval = $dateCIN->diff($dateActuel);

               if($interval->y >= 10){
                   array_push($erreur, 'CIN (expirer)');
               }
           }

            if(!$residence)
                array_push($erreur, 'Certificat de Résidence');
            else{
                $dateResidence = $residence->getDate();

                $interval = $dateResidence->diff($dateActuel);

                if(($interval->y == 1 and $interval->m >= 1 ) or $interval->y > 1){
                    array_push($erreur, 'Certificat de Résidence (expirer)');
                }
            }

            if($employe->getImageUrl() == 'user.jpg'){
                array_push($erreur, 'Pas de photo');

            }

           $tabEmploye = array(
               'employe' => $employe,
               'erreurs' => $erreur
           );

           array_push($tabEmployes, $tabEmploye);
        }

        return $this->render('@Employe/employe/test_dossier.html.twig', array(
            'tabEmployes' => $tabEmployes,
        ));
    }


    /**
     * Lists all Employe entities.
     *
     * @Route("/", name="employe_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $employes = $em->getRepository('EmployeBundle:Employe')->findBy(
            array('etat' => true),
            array('nom' => 'asc')
        );

        return $this->render('@Employe/employe/index.html.twig', array(
            'employes' => $employes,
        ));
    }

    /**
     * Lists all Employe entities.
     *
     * @Route("/innactive", name="employe_index_innactive")
     * @Method("GET")
     */
    public function indexInnactiveAction()
    {
        $em = $this->getDoctrine()->getManager();

        $employes = $em->getRepository('EmployeBundle:Employe')->findBy(
            array('etat' => false),
            array('nom' => 'asc')
        );

        return $this->render('@Employe/employe/index_innactive.html.twig', array(
            'employes' => $employes,
        ));
    }


    /**
     * Creates a new Employe entity.
     *
     * @Route("/nouveau", name="employe_ajouter")
     * @Method({"GET", "POST"})
     */
    public function ajouterAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $employe = new Employe();
        $form = $this->createForm('EmployeBundle\Form\EmployeType', $employe);
        $form->handleRequest($request);

        $repository_user = $em->getRepository('UserBundle:User');
        $user_liste = $repository_user->findBy(
            array('enabled' => true),
            array('username' => 'asc')
        );

        $repository_societe = $em->getRepository('EmployeBundle:Societe');

        $societeList = $repository_societe->findBy(
            array(),
            array('nom' => 'asc')
        );

        if ($form->isSubmitted() && $form->isValid()) {
            $employe->setNomComplet($employe->getNom());

            //----DATE DE DEBUT EMPLOYE ET RESTE CONGE----
            $date_debut = \ DateTime::createFromFormat('d/m/Y', $_POST['dateDebutTravail']);
            $employe->setDateDebut($date_debut);

            $nombreMoisService = $this->moisDeServiceAction($date_debut);

            //------------------------

            if($employe->getPrenom()){
                $employe->setNomComplet($employe->getNom().' '.$employe->getPrenom());
            }

            // ------------------ EDIT UTILISATEUR NON OFFICIEL ------------------

            if(isset($_POST['nonOfficiel'])){
                $employe->setNonOfficiel(true);
            }

            // ------------------///// EDIT UTILISATEUR NON OFFICIEL /////------------------


            //----COMPTE EMPLOYE-----
            $comptePresence = new Compte_Presence();
            $comptePaie = new Compte_Paie();

            $comptePaie->setSlug($this->randomLettre(12));
            $comptePresence->setSlug($this->randomLettre(12));
            $employe->setSlug($this->randomLettre(12));


            $comptePresence->setResteConge($nombreMoisService * 2.5);

            if (isset($_POST['reste_conge']) and $_POST['reste_conge'] > 0)
                $comptePresence->setResteConge($_POST['reste_conge']);


            $employe->setComptePaie($comptePaie);
            $employe->setComptePresence($comptePresence);
            //----////COMPTE EMPLOYE////-----

            //------image de profil-------

            if($_FILES['image']['size'] > 0){
                if($image = $this->uploadImageProfil('image')){
                    $employe->setImageUrl($image);

                }else{
                    return new Response('Erreur! Erreur dans le téléchargement de l\'images..');
                }
            }
            if(isset($_POST['banque']) and $_POST['banque'] !=''){
                $employe->setNomBanque($_POST['banque']);
            }
            if(isset($_POST['numero']) and $_POST['numero'] !=''){
                $employe->setNumeroCompte($_POST['numero']);
            }
            if(isset($_POST['beneficiaire']) and $_POST['beneficiaire'] !=''){
                $employe->setBeneficiaire($_POST['beneficiaire']);
            }

            //------////image de profil////-------

            //------RESIDENCE & CIN-------

            if($_FILES['fichierCIN']['size'] > 0 and isset($_POST['dateCIN'])){
                $cin = new Emp_cin();

                if ($fichierCIN = $cin->uploadFichier('fichierCIN')){
                    $cin->setUrl($fichierCIN);
                    $cin->setDate(\ DateTime::createFromFormat('d/m/Y', $_POST['dateCIN']));
                    $cin->setEmploye($employe);

                    $em->persist($cin);
                }

            }

            if($_FILES['fichierResidence']['size'] > 0 and isset($_POST['dateResidence'])){
                $residence = new Emp_residence();

                if ($fichierResidence = $residence->uploadFichier('fichierResidence')){
                    $residence->setUrl($fichierResidence);
                    $residence->setDate(\ DateTime::createFromFormat('d/m/Y', $_POST['dateResidence']));
                    $residence->setEmploye($employe);

                    $em->persist($residence);
                }

            }

            //------/////RESIDENCE & CIN////-------

            //-----SET USERNAME-------

            if ($_POST['username'] != '--- Non-défini ---'){
                $userCompte = $repository_user->findOneBy(array('username' => $_POST['username']));

                $employe->setUserCompte($userCompte);
            }else{
                $employe->setUserCompte(null);
            }

            //------------------------

            //------SET SOCIETE-----

            $societe = $repository_societe->findOneBy(
                array('nom' => $_POST['societe'])
            );

            $employe->setSociete($societe);

            //------//////SET SOCIETE//////-----

            $em->persist($employe);
            $em->flush();

            //            ---HISTORIQUE GLOBAL-----

            $historiqueGlobal = new Historique_Global();

            $historiqueGlobal->setLibelle('Ajout d\'un nouveau employé '.$employe->getNomComplet());
            $historiqueGlobal->setDate(new \DateTime());
            $historiqueGlobal->setLien($this->generateUrl('employe_voir', array('slug' => $employe->getSlug())));
            $historiqueGlobal->setUser($this->getUser());

            $em->persist($historiqueGlobal);

            //            ---HISTORIQUE GLOBAL-----

            return $this->redirectToRoute('employe_voir', array('slug' => $employe->getSlug()));

        }

        return $this->render('@Employe/employe/ajouter.html.twig', array(
            'employe' => $employe,
            'form' => $form->createView(),
            'userList' => $user_liste,
            'societeList' => $societeList,
        ));
    }

    /**
     * Finds and displays a Employe entity.
     *
     * @Route("/profil/{slug}", name="employe_voir")
     * @Method("GET")
     */
    public function voirAction(Employe $employe)
    {
        $em = $this->getDoctrine()->getManager();

        $repository_document = $em->getRepository('DocumentBundle:Document');
        $repository_absence = $em->getRepository('PresenceBundle:Absence');
        $repository_cin = $em->getRepository('EmployeBundle:Emp_cin');
        $repository_residence = $em->getRepository('EmployeBundle:Emp_residence');

        $cin = $repository_cin->findOneBy(array('employe' => $employe));
        $residence = $repository_residence->findOneBy(array('employe' => $employe));


        $documents = $repository_document->findBy(
            array('employe' => $employe),
            array('nom' => 'asc')
        );

        $liste_absence = $repository_absence->findBy(array(
            'employe' => $employe
        ));

        $dateActuel = new \DateTime();

        $nb_absence = 0;
        foreach ($liste_absence as $absence){
            if($absence->getDateDebut()->format('Y') == $dateActuel->format('Y')) $nb_absence ++;
        }

        return $this->render('@Employe/employe/voir.html.twig', array(
            'employe' => $employe,
            'documents' => $documents,
            'nb_absence' => $nb_absence,
            'cin' => $cin,
            'residence' => $residence,
        ));
    }

    /**
     * Displays a form to edit an existing Employe entity.
     *
     * @Route("/{slug}/modifier", name="employe_modifier")
     * @Method({"GET", "POST"})
     */
    public function modifierAction(Request $request, Employe $employe)
    {
        $em = $this->getDoctrine()->getManager();

        $editForm = $this->createForm('EmployeBundle\Form\EmployeType', $employe);
        $editForm->handleRequest($request);

        $repository_user = $em->getRepository('UserBundle:User');
        $user_liste = $repository_user->findBy(
            array('enabled' => true),
            array('username' => 'asc')
        );

        $repository_societe = $em->getRepository('EmployeBundle:Societe');

        $societeList = $repository_societe->findBy(
            array(),
            array('nom' => 'asc')
        );

        if ($editForm->isSubmitted() && $editForm->isValid()) {

            //------image de profil-------

            if($_FILES['image']['size'] > 0){
                if($image = $this->uploadImageProfil('image')){
                    $employe->setImageUrl($image);

                }else{
                    return new Response('Erreur! Erreur dans le téléchargement de l\'images..');
                }
            }

            //------////image de profil////-------

            //-----SET USERNAME-------
            if ($_POST['username'] != '--- Non-défini ---'){
                $userCompte = $repository_user->findOneBy(array('username' => $_POST['username']));

                $employe->setUserCompte($userCompte);
            }else{
                $employe->setUserCompte(null);
            }

            //------------------------

            //------SET SOCIETE-----

            $societe = $repository_societe->findOneBy(
                array('nom' => $_POST['societe'])
            );

            $employe->setSociete($societe);

            if(isset($_POST['banque'])){
                $employe->setNomBanque($_POST['banque']);
            }
            if(isset($_POST['numero'])){
                $employe->setNumeroCompte($_POST['numero']);
            }
            if(isset($_POST['beneficiaire'])){
                $employe->setBeneficiaire($_POST['beneficiaire']);
            }

            $employe->setNomComplet($employe->getNom());
            if($employe->getPrenom()){
                $employe->setNomComplet($employe->getNom().' '.$employe->getPrenom());
            }

            //------//////SET SOCIETE//////-----

            //----------------- COTISATION INNACTIF ------------------

            if (isset($_POST['innactifCnaps'])){
                $employe->setInnactifCnaps(true);
            }
            else{
                $employe->setInnactifCnaps(false);

            }

            if(isset($_POST['innactifOstie'])){
                $employe->setInnactifOstie(true);
//                return new Response($_POST['pourcentEmployeur']);
                $employe->setPoucentOrgSatTrav($_POST['pourcentTravailleur']);
                $employe->setPoucentOrgSatEmpl($_POST['pourcentEmployeur']);
            }
            else{
                $employe->setInnactifOstie(false);
                $employe->setPoucentOrgSatTrav(null);
                $employe->setPoucentOrgSatEmpl(null);
            }


            //-----------------//// COTISATION INNACTIF ////------------------

            $em->persist($employe);

            $valueTest = array();

            $my_test = $employe->getTestModification();


            if($my_test['nom'] != $employe->getNom()){
                $valueTest[] = array(
                    'libelle' => 'Nom',
                    'ancien' => $my_test['nom'],
                    'nouveau' => $employe->getNom()
                );
            }

            if($my_test['prenom'] != $employe->getPrenom()){
                $valueTest[] = array(
                    'libelle' => 'Prénom',
                    'ancien' => $my_test['prenom'],
                    'nouveau' => $employe->getPrenom()
                );
            }

            if($my_test['addresse'] != $employe->getAdresse()){
                $valueTest[] = array(
                    'libelle' => 'Adresse',
                    'ancien' => $my_test['addresse'],
                    'nouveau' => $employe->getAdresse()
                );
            }

            if($my_test['poste'] != $employe->getPoste()){
                $valueTest[] = array(
                    'libelle' => 'Poste',
                    'ancien' => $my_test['poste'],
                    'nouveau' => $employe->getPoste()
                );
            }
            if($my_test['matricul'] != $employe->getMatricul()){
                $valueTest[] = array(
                    'libelle' => 'Matricule',
                    'ancien' => $my_test['matricul'],
                    'nouveau' => $employe->getMatricul()
                );
            }
            if($my_test['contact'] != $employe->getContact()){
                $valueTest[] = array(
                    'libelle' => 'Télephone',
                    'ancien' => $my_test['contact'],
                    'nouveau' => $employe->getContact()
                );
            }

            if($my_test['email'] != $employe->getEmail()){
                $valueTest[] = array(
                    'libelle' => 'Email',
                    'ancien' => $my_test['email'],
                    'nouveau' => $employe->getEmail()
                );
            }

            if($my_test['ostie'] != $employe->getOstie()){
                $valueTest[] = array(
                    'libelle' => 'Ostie',
                    'ancien' => $my_test['ostie'],
                    'nouveau' => $employe->getOstie()
                );
            }

            if($my_test['cnaps'] != $employe->getCnaps()){
                $valueTest[] = array(
                    'libelle' => 'CNAPS',
                    'ancien' => $my_test['cnaps'],
                    'nouveau' => $employe->getCnaps()
                );
            }

            if($my_test['salaire'] != $employe->getSalaire()){
                $valueTest[] = array(
                    'libelle' => 'Salaire',
                    'ancien' => $my_test['salaire'],
                    'nouveau' => $employe->getSalaire()
                );
            }

            if($my_test['societe'] != $employe->getSociete()->getNom()){
                $valueTest[] = array(
                    'libelle' => 'Société d\'affectation',
                    'ancien' => $my_test['societe'],
                    'nouveau' => $employe->getSociete()->getNom()
                );
            }
            if($my_test['banque'] != $employe->getNomBanque()){
                $valueTest[] = array(
                    'libelle' => 'Nom du banque',
                    'ancien' => $my_test['banque'],
                    'nouveau' => $employe->getNomBanque()
                );
            }
            if($my_test['numero'] != $employe->getNumeroCompte()){
                $valueTest[] = array(
                    'libelle' => 'Numéro compte',
                    'ancien' => $my_test['numero'],
                    'nouveau' => $employe->getNumeroCompte()
                );
            }
            if($my_test['beneficiaire'] != $employe->getBeneficiaire()){
                $valueTest[] = array(
                    'libelle' => 'Bénéficiaire',
                    'ancien' => $my_test['beneficiaire'],
                    'nouveau' => $employe->getBeneficiaire()
                );
            }

            // ------------------ EDIT UTILISATEUR NON OFFICIEL ------------------

            if(isset($_POST['nonOfficiel'])){
                $employe->setNonOfficiel(true);
            }

            // ------------------///// EDIT UTILISATEUR NON OFFICIEL /////------------------

            if(count($valueTest) > 0){

                $historiqueGlobal = new Historique_Global();

                $historiqueGlobal->setLibelle('Modification des informations de l\'employé: '.$employe->getNomComplet());
                $historiqueGlobal->setDate(new \DateTime());
                $historiqueGlobal->setLien($this->generateUrl('employe_voir', array('slug' => $employe->getSlug())));
                $historiqueGlobal->setUser($this->getUser());
                $historiqueGlobal->setModification($valueTest);

                $em->persist($historiqueGlobal);
            }

            $em->flush();

            return $this->redirectToRoute('employe_voir', array('slug' => $employe->getSlug()));
        }else{
            $username = 'non-définie';
            if ($employe->getUserCompte()){
                $username = $employe->getUserCompte()->getUsername();
            }
            $tab_test = array(
                'nom' => $employe->getNom(),
                'prenom' => $employe->getPrenom(),
                'addresse' => $employe->getAdresse(),
                'poste' => $employe->getPoste(),
                'matricul' => $employe->getMatricul(),
                'contact' => $employe->getContact(),
                'email' => $employe->getEmail(),
                'ostie' => $employe->getOstie(),
                'cnaps' => $employe->getCnaps(),
                'salaire' => $employe->getSalaire(),
                'societe' => $employe->getSociete()->getNom(),
                'utilisateur' => $username,
                'banque'=>$employe->getNomBanque(),
                'beneficiaire'=>$employe->getBeneficiaire(),
                'numero'=>$employe->getNumeroCompte(),
            );

            $employe->setTestModification($tab_test);

            $em->persist($employe);
            $em->flush();
        }

        return $this->render('@Employe/employe/modifier.html.twig', array(
            'employe' => $employe,
            'form' => $editForm->createView(),
            'userList' => $user_liste,
            'societeList' => $societeList,
        ));
    }

    /**
     * Displays a form to edit an existing Employe entity.
     *
     * @Route("/{slug}/upload/file", name="employe_upload_fichier")
     * @Method({"GET", "POST"})
     */
    public function uploadDocumentAction(Employe $employe){
        $em = $this->getDoctrine()->getManager();

        $request = $this->get('request');

        if($request->getMethod() == 'POST'){
            $document = new Document();
            $document->setNom($_POST['nom']);

            $nomFichier = $document->uploadFichier('fichier');

            if($nomFichier){
                $document->setUrl($nomFichier);
            }else return new Response('Erreur!! Veuillez réessaier..');

            $document->setEmploye($employe);

            $em->persist($document);

//            ------------HISTORIQUE GLOBAL------------

            $historiqueGlobal = new Historique_Global();

            $historiqueGlobal->setLibelle('Upload d\'un nouveau document: '.$document->getNom().'pour '.$employe->getNomComplet());
            $historiqueGlobal->setDate(new \DateTime());
            $historiqueGlobal->setLien($this->generateUrl('document_afficher', array('url' => $document->getUrl())));
            $historiqueGlobal->setUser($this->getUser());

            $em->persist($historiqueGlobal);

//            ------------HISTORIQUE GLOBAL------------

            $em->flush();

            return $this->redirect($this->generateUrl('employe_voir', array(
                'slug'=> $employe->getSlug()
            )));

        }

        return new Response('Erreur 404');
    }

    /**
     * Displays a form to edit an existing Employe entity.
     *
     * @Route("/{slug}/changer/etat", name="employe_changer_etat")
     * @Method({"GET", "POST"})
     */
    public function changerEtatAction(Employe $employe){
        $em = $this->getDoctrine()->getManager();


        if($employe->getEtat()){
            $employe->setEtat(false);
            $dateDebauche = \DateTime::createFromFormat('d/m/Y', $_POST['date_debauche']);
            $employe->setDateDebauche($dateDebauche);
        }
        else{
            $employe->setEtat(true);
            $employe->setDateDebauche(null);
        }

        $em->persist($employe);

//            ------------HISTORIQUE GLOBAL------------

        $historiqueGlobal = new Historique_Global();

        $etat = 'Désactivion';
        if($employe->getEtat()) $etat = 'Réctivation';

        $historiqueGlobal->setLibelle($etat.' du compte employé'.$employe->getNomComplet());
        $historiqueGlobal->setDate(new \DateTime());
        $historiqueGlobal->setLien($this->generateUrl('employe_voir', array(
            'slug'=> $employe->getSlug()
        )));
        $historiqueGlobal->setUser($this->getUser());

        $em->persist($historiqueGlobal);

//            ------------HISTORIQUE GLOBAL------------

        $em->flush();

        return $this->redirect($this->generateUrl('employe_voir', array(
            'slug'=> $employe->getSlug()
        )));

    }

    /**
     * LISTE DES EMPLOYES
     *
     * @Route("/mon-profil/liste", name="employe_mon_profil")
     * @Method("GET")
     */
    public function profilUserAction()
    {
        $em = $this->getDoctrine()->getManager();

        $listeEmploye = $em->getRepository('EmployeBundle:Employe')->findBy(
            array('userCompte' => $this->getUser()),
            array('nom' => 'asc')
        );

        return $this->render('@Employe/employe/mon_profil.html.twig', array(
            'listeEmploye' => $listeEmploye
        ));
    }

    /**
     * NOUVEAU DOCUMENT ADMINISTRATIVE
     *
     * @Route("/insérer/{id}/document-administrative", name="employe_upload_doc_admin")
     * @Method({"GET", "POST"})
     */
    public function uploadDocumentAdministrativeAction(Employe $employe){
        $request = $this->get('request');

        if ($request->getMethod() == 'POST'){

            $em = $this->getDoctrine()->getManager();

            $repository_cin = $em->getRepository('EmployeBundle:Emp_cin');

            $repository_residence = $em->getRepository('EmployeBundle:Emp_residence');

            $residence = $repository_residence->findOneBy(array(
                'employe' => $employe
            ));

            $cin = $repository_cin->findOneBy(array(
                'employe' => $employe
            ));

            //------RESIDENCE & CIN-------

            if($_FILES['fichierCIN']['size'] > 0 and isset($_POST['dateCIN'])){

                if (!$cin){
                    $test_cin = '(Vide)';
                    $cin = new Emp_cin();
                }else {
                    $_cin = clone $cin;
                    $test_cin = $_cin->getDate()->format('d/m/Y');
                }

                if ($fichierCIN = $cin->uploadFichier('fichierCIN')){
                    $cin->setUrl($fichierCIN);
                    $cin->setDate(\ DateTime::createFromFormat('d/m/Y', $_POST['dateCIN']));
                    $cin->setEmploye($employe);

                    $em->persist($cin);
                }
            }

            if($_FILES['fichierResidence']['size'] > 0 and isset($_POST['dateResidence'])){

                if(!$residence){
                    $test_residence = '(Vide)';
                    $residence = new Emp_residence();
                }else{
                    $_residence = clone $residence;
                    $test_residence = $_residence->getDate()->format('d/m/Y');
                }

                if ($fichierResidence = $residence->uploadFichier('fichierResidence')){
                    $residence->setUrl($fichierResidence);
                    $residence->setDate(\ DateTime::createFromFormat('d/m/Y', $_POST['dateResidence']));
                    $residence->setEmploye($employe);

                    $em->persist($residence);
                }
            }

//            ------------HISTORIQUE GLOBAL------------



            if($cin or $residence){

                $valueTest = array();

                if($cin){
                    array_push($valueTest, array(
                        'libelle' => 'Date CIN',
                        'ancien' => $test_cin,
                        'nouveau' => $cin->getDate()->format('d/m/Y')
                    ));
                }

                if($residence){
                    array_push($valueTest, array(
                        'libelle' => 'Date Résidence',
                        'ancien' => $test_residence,
                        'nouveau' => $residence->getDate()->format('d/m/Y')
                    ));
                }

                $historiqueGlobal = new Historique_Global();

                $historiqueGlobal->setLibelle('Actualisation des données administratives CIN et Résidence de: '.$employe->getNomComplet());
                $historiqueGlobal->setDate(new \DateTime());
                $historiqueGlobal->setLien($this->generateUrl('employe_voir', array('slug' => $employe->getSlug())));
                $historiqueGlobal->setUser($this->getUser());
                $historiqueGlobal->setModification($valueTest);

                $em->persist($historiqueGlobal);

//            ------------HISTORIQUE GLOBAL------------

                $em->flush();
            }


            return $this->redirect($this->generateUrl('employe_voir', array('slug' => $employe->getSlug())));

            //------/////RESIDENCE & CIN////-------
        }

        return new Response('404 NOT-FOUND');
    }

}

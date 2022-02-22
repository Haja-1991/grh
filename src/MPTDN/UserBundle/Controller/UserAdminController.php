<?php
/**
 * Created by PhpStorm.
 * User: Allin
 * Date: 01/02/2018
 * Time: 12:29
 */

namespace MPTDN\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use MPTDN\UserBundle\Entity\User;
use MPTDN\UserBundle\Form\UserType;
use Symfony\Component\HttpFoundation\Response;


class UserAdminController extends Controller
{


    public function indexAction(){
        $em = $this->getDoctrine()->getManager();
        $res = $em->getRepository('UserBundle:User');

        $users = $res->findAll();

        $user = new User();
        $form = $this->createForm(new UserType(), $user);


        return $this->render('UserBundle:MyAdmin:liste.html.twig',array(
            'users' => $users,
            'form' => $form->createView(),
        ));

    }

    public function modifierAction(User $user){

        $form = $this->createFormBuilder($user)
            ->add('username', null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle'))
            ->add('nom', 'text', array(
                'label' => 'Nom:',
            ))
            ->add('email', 'email', array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'))

            ->getForm();

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $rol = $_POST['role'];
                $user->setRoles(array($rol));

                if($user->ifRole('ROLE_ADMIN')){
                    $user->addRole('ROLE_USER');
                    $user->addRole('ROLE_RH');
                    $user->addRole('ROLE_PAIE');
                    $user->addRole('ROLE_ADMIN');
                }

                if($user->ifRole('ROLE_RH')){
                    $user->addRole('ROLE_USER');
                    $user->addRole('ROLE_RH');
                    $user->addRole('ROLE_PAIE');
                }

                if($user->ifRole('ROLE_SEC')){
                    $user->addRole('ROLE_USER');
                }

                $em->persist($user);
                $em->flush();


                //flash notification
                $this->addFlash(
                    'succes',
                    'Les informations sur l\'utilisateur a été mis à jour...'
                );

                return $this->redirect($this->generateUrl('admin_homepage'));

            }
        }

        return $this->render('UserBundle:MyAdmin:user_modfier.html.twig',
            array(
                'form' => $form->createView(),
                'user' => $user,
            )
        );


    }

    public function modifierMDPAction(User $user){

        $form = $this->createFormBuilder($user)
            ->add('username', null, array('label' => 'form.username', 'translation_domain' => 'FOSUserBundle'))
            ->add('nom', 'text', array(
                'label' => 'Nom:',
            ))
            ->add('email', 'email', array('label' => 'form.email', 'translation_domain' => 'FOSUserBundle'))
            ->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'options' => array('translation_domain' => 'FOSUserBundle'),
                'first_options' => array('label' => 'form.password'),
                'second_options' => array('label' => 'form.password_confirmation'),
                'invalid_message' => 'fos_user.password.mismatch',
            ))

            ->getForm();



        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {

            $em = $this->getDoctrine()->getManager();

            $user->setPlainPassword($_POST['mdp']);

            $userMager = $this->container->get('fos_user.user_manager');

            $userMager->updateUser($user, true);

            $em->persist($user);
            $em->flush();

            //flash notification
            $this->addFlash(
                'succes',
                'Le mot de passe l\'utilisateur a été modifié...'
            );

            return $this->redirect($this->generateUrl('admin_user_modifier', array('id' => $user->getId())));


        }



        return $this->render('UserBundle:MyAdmin:mdp_modifier.html.twig',
            array(
                'form' => $form->createView(),
                'user' => $user,
            )
        );


    }

    public function inscriptionAction()
    {
        $user = new User();
        $form = $this->createForm(new UserType(), $user);
        //$user->setRoles(array('ROLE_USER'));
        $user->setEnabled(true);

        $request = $this->get('request');

        if ($request->getMethod() == 'POST') {
            $form->bind($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $rol = $_POST['role'];
                $user->setRoles(array($rol));

                if($user->ifRole('ROLE_ADMIN')){
                    $user->addRole('ROLE_USER');
                    $user->addRole('ROLE_RH');
                    $user->addRole('ROLE_PAIE');
                    $user->addRole('ROLE_ADMIN');
                }

                if($user->ifRole('ROLE_RH')){
                    $user->addRole('ROLE_USER');
                    $user->addRole('ROLE_RH');
                    $user->addRole('ROLE_PAIE');
                }

                if($user->ifRole('ROLE_SEC')){
                    $user->addRole('ROLE_USER');
                }

                $em->persist($user);
                $em->flush();

                //flash notification
                $this->addFlash(
                    'succes',
                    'Utilisateur ajouté avec succées...'
                );

                return $this->redirect($this->generateUrl('admin_homepage'));
            }
        }



        return $this->render('UserBundle:MyAdmin:user_inscription.html.twig',
            array(
                'form' => $form->createView(),
            )
        );

    }


    public function supprimerAction(User $user ){

        //utilisateur en cour
        $usr = $this->getUser();

        $em = $this->getDoctrine()->getManager();
        $em->remove($user);

        $em->flush();
//
//        //-----Historique------------
//        $historique = new Historique();
//        $historique->setUser($usr);
//        $historique->setLibelle('Suppression de l\'Utilisateur: '.$user->getUsername());
//
//        $em->persist($historique);
//        $em->flush();

        //flash notification
        $this->addFlash(
            'danger',
            'Utilisateur supprimé avec succées...'
        );

        return $this->redirect($this->generateUrl('admin_homepage'));


    }

    public  function activerAction(User $user){

        $user->setEnabled(true);


        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();
//
//        //utilisateur en cour
//        $usr = $this->getUser();
//
//        //-----Historique------------
//        $historique = new Historique();
//        $historique->setUser($usr);
//        $historique->setLibelle('Réactivation du compte Utilisateur: '.$user->getUsername());
//        $em->persist($historique);
//        $em->flush();

        //flash notification
        $this->addFlash(
            'modalVert',
            'Le compte a été activé!'
        );

        return $this->redirect($this->generateUrl('admin_homepage'));
    }

    public  function desactiverAction(User $user){

        $user->setEnabled(false);

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

//        //utilisateur en cour
//        $usr = $this->getUser();
//
//        //-----Historique------------
//        $historique = new Historique();
//        $historique->setUser($usr);
//        $historique->setLibelle('Désactivation du compte Utilisateur: '.$user->getUsername());
//        $em->persist($historique);
//        $em->flush();

        //flash notification
        $this->addFlash(
            'modalRouge',
            'Le compte a été Désactivé!'
        );

        return $this->redirect($this->generateUrl('admin_homepage'));
    }

//
//    public function choixRoleAction(User $user){
//
//        $request = $this->get('request');
//
//        if($request->getMethod() == 'POST'){
//
//
//            if(isset($_POST['client'])){
//                if($user->ifRole('ROLE_CLIENT') != true){
//                    $user->addRole('ROLE_CLIENT');
//                }
//            }else{
//                if($user->ifRole('ROLE_CLIENT') == true){
//                    $user->removeRole('ROLE_CLIENT');
//                }
//            }
//
//            if(isset($_POST['fournisseur'])){
//                if($user->ifRole('ROLE_FOURNISSEUR') != true){
//                    $user->addRole('ROLE_FOURNISSEUR');
//                }
//            }else{
//                if($user->ifRole('ROLE_FOURNISSEUR') == true){
//                    $user->removeRole('ROLE_FOURNISSEUR');
//                }
//            }
//
//            if(isset($_POST['divers'])){
//                if($user->ifRole('ROLE_DIVERS') != true){
//                    $user->addRole('ROLE_DIVERS');
//                }
//            }else{
//                if($user->ifRole('ROLE_DIVERS') == true){
//                    $user->removeRole('ROLE_DIVERS');
//                }
//            }
//
//            if(isset($_POST['prive'])){
//                if($user->ifRole('ROLE_PRIVE') != true){
//                    $user->addRole('ROLE_PRIVE');
//                }
//            }else{
//                if($user->ifRole('ROLE_PRIVE') == true){
//                    $user->removeRole('ROLE_PRIVE');
//                }
//            }
//
//            $em = $this->getDoctrine()->getManager();
//            $em->persist($user);
//            $em->flush();
//
//            $this->addFlash(
//                'succes',
//                'Les rôes de l\'utilisateur modifier...'
//            );
//
//            return $this->redirect($this->generateUrl('admin_user_modifier', array('id' => $user->getId())));
//        }
//
//        return new Response('Erreur de traitement');
//    }

}
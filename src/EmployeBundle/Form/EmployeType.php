<?php

namespace EmployeBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EmployeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', 'text',array(
                'label' => 'Nom',
                'attr' => array(
                    'class' => 'form-control',
                    'required',
                )
            ))
            ->add('prenom', 'text',array(
                'label' => 'Prénom',
                'attr' => array(
                    'class' => 'form-control',
                    'required',
                )
            ))
            ->add('numCin', 'text',array(
                'label' => 'N° CIN',
                'attr' => array(
                    'class' => 'form-control',
                    'required',
                )
            ))
            ->add('adresse', 'text',array(
                'attr' => array(
                    'class' => 'form-control',
                    'required',
                )
            ))
            ->add('poste', 'text',array(
                'attr' => array(
                    'class' => 'form-control',
                    'required',
                )
            ))
            ->add('matricul', 'text',array(
                'label' => 'N° matricule',
                'attr' => array(
                    'class' => 'form-control',
                    'required',
                )
            ))
            ->add('contact', 'text',array(
                'label' => 'Téléphone',
                'attr' => array(
                    'class' => 'form-control',
                    'required',
                )
            ))
            ->add('email', 'text',array(
                'label' => 'Email',
                'attr' => array(
                    'class' => 'form-control',
                    'required',
                )
            ))

            ->add('ostie', 'text',array(
                'label' => 'N° OSTIE',
                'attr' => array(
                    'class' => 'form-control',
                    'required',
                )
            ))
            ->add('cnaps', 'text',array(
                'label' => 'N° CNAPS',
                'attr' => array(
                    'class' => 'form-control',
                    'required',
                )
            ))

            ->add('salaire', 'text',array(
                'label' => 'Salaire',
                'attr' => array(
                    'class' => 'form-control only_float not_paie',
                    'required',
                )
            ))
            ->add('nbEnfant', 'text',array(
                'label' => 'Nombre d\'enfant',
                'attr' => array(
                    'class' => 'form-control only_float not_paie',
                    'required',
                )
            ))

            ->add('departement', 'text',array(
                'label' => 'Département',
                'attr' => array(
                    'class' => 'form-control not_paie',
                    'required',
                )
            ))
            ->add('numCin', 'text',array(
                'label' => 'Numero CIN',
                'attr' => array(
                    'class' => 'form-control not_paie',
                    'required',
                )
            ))
            ->add('categorie', 'text',array(
                'label' => 'Catégorie',
                'attr' => array(
                    'class' => 'form-control not_paie',
                    'required',
                )
            ))
            ->add('methodePaiement', 'choice',array(
                'label' => 'Prise de paie parl',
                'attr' => array(
                    'id'=>'paiement',
                    'class' => 'form-control not_paie',
                    'required',
                ),
                'choices'=>array(
                    'Espèces' => 'Espèces',
                    'Virement' => 'Virement',
                    'Chèque' => 'Chèque',
                )
            ))
          ;
            $builder->get('matricul')->setRequired(false);
            $builder->get('email')->setRequired(false);
            $builder->get('ostie')->setRequired(false);
            $builder->get('cnaps')->setRequired(false);
            $builder->get('nbEnfant')->setRequired(false);
            $builder->get('categorie')->setRequired(false);
            $builder->get('numCin')->setRequired(false);
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'EmployeBundle\Entity\Employe'
        ));
    }
}

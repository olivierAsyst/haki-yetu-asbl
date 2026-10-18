<?php

namespace App\Form;

use App\Entity\Rapport;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class RapportType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre du rapport',
                'attr' => [
                    'placeholder' => 'Ex: Rapport annuel 2024',
                    'class' => 'input-modern',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Description du rapport',
                    'rows' => 5,
                    'class' => 'input-modern',
                ],
            ])
            ->add('annee', TextType::class, [
                'label' => 'Année',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ex: 2024',
                    'class' => 'input-modern',
                ],
            ])
            ->add('periode', TextType::class, [
                'label' => 'Période ou exercice',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ex: Exercice 2023-2024',
                    'class' => 'input-modern',
                ],
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type de rapport',
                'choices' => [
                    'Rapport annuel' => 'annuel',
                    'Rapport financier' => 'financier',
                    'Rapport d\'activités' => 'activites',
                    'Rapport d\'évaluation' => 'evaluation',
                    'Rapport de projet' => 'projet',
                    'Autre' => 'autre',
                ],
                'placeholder' => 'Sélectionnez un type',
                'required' => false,
                'attr' => [
                    'class' => 'input-modern',
                ],
            ])
            ->add('fichierFile', VichFileType::class, [
                'label' => 'Fichier du rapport',
                'required' => false,
                'allow_delete' => true,
                'download_label' => 'Télécharger',
                'download_uri' => true,
                'asset_helper' => true,
                'attr' => [
                    'class' => 'input-modern',
                ],
            ])
            ->add('datePublication', DateType::class, [
                'label' => 'Date de publication',
                'widget' => 'single_text',
                'required' => false,
                'attr' => [
                    'class' => 'input-modern',
                ],
            ])
            ->add('langue', ChoiceType::class, [
                'label' => 'Langue',
                'choices' => [
                    'Français' => 'fr',
                    'Anglais' => 'en',
                    'Swahili' => 'sw',
                    'Lingala' => 'ln',
                    'Autre' => 'autre',
                ],
                'placeholder' => 'Sélectionnez une langue',
                'required' => false,
                'attr' => [
                    'class' => 'input-modern',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rapport::class,
        ]);
    }
}
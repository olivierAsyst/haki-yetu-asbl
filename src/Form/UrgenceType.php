<?php

namespace App\Form;

use App\Entity\Urgence;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UrgenceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $typesUrgence = [
            'Catastrophe naturelle' => 'catastrophe_naturelle',
            'Conflit armé' => 'conflit_arme',
            'Crise sanitaire' => 'crise_sanitaire',
            'Insécurité alimentaire' => 'insecurite_alimentaire',
            'Déplacement forcé' => 'deplacement_force',
            'Urgence éducative' => 'urgence_educative',
            'Autre' => 'autre',
        ];

        $statuts = [
            'En cours' => 'en_cours',
            'Collecte terminée' => 'collecte_terminee',
            'Urgence terminée' => 'urgence_terminee',
            'Suspendu' => 'suspendu',
        ];

        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre de l\'urgence',
                'attr' => [
                    'placeholder' => 'Ex: Urgence - Inondations à Uvira',
                    'class' => 'form-input',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description détaillée',
                'attr' => [
                    'rows' => 6,
                    'placeholder' => 'Décrivez la situation d\'urgence, les besoins et l\'impact sur la communauté...',
                    'class' => 'form-textarea',
                ],
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type d\'urgence',
                'choices' => $typesUrgence,
                'attr' => ['class' => 'form-select'],
            ])
            ->add('lieu', TextType::class, [
                'label' => 'Lieu de l\'urgence',
                'attr' => [
                    'placeholder' => 'Ex: Uvira, Sud-Kivu',
                    'class' => 'form-input',
                ],
            ])
            ->add('montantCible', MoneyType::class, [
                'label' => 'Montant cible (CDF)',
                'currency' => 'CDF',
                'scale' => 0,
                'attr' => [
                    'placeholder' => 'Ex: 5000000',
                    'class' => 'form-input',
                    'min' => 1000,
                ],
            ])
            ->add('montantCollecte', MoneyType::class, [
                'label' => 'Montant déjà collecté (CDF)',
                'currency' => 'CDF',
                'scale' => 0,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ex: 1000000',
                    'class' => 'form-input',
                    'min' => 0,
                ],
            ])
            ->add('dateDebut', DateType::class, [
                'label' => 'Date de début',
                'widget' => 'single_text',
                'html5' => true,
                'attr' => ['class' => 'form-input'],
            ])
            ->add('dateFin', DateType::class, [
                'label' => 'Date de fin prévue',
                'widget' => 'single_text',
                'html5' => true,
                'required' => false,
                'attr' => ['class' => 'form-input'],
            ])
            ->add('statut', ChoiceType::class, [
                'label' => 'Statut',
                'choices' => $statuts,
                'attr' => ['class' => 'form-select'],
            ])
            ->add('estActive', ChoiceType::class, [
                'label' => 'Active ?',
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'expanded' => true,
                'multiple' => false,
                'attr' => ['class' => 'form-radio'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Urgence::class,
        ]);
    }
}
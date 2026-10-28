<?php

namespace App\Form;

use App\Entity\RelationMedia;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RelationMediaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre de l\'article *',
                'attr' => [
                    'placeholder' => 'Ex: L\'inclusion des personnes handicapées à Uvira',
                    'class' => 'modern-input'
                ]
            ])
            ->add('nomDeMedia', TextType::class, [
                'label' => 'Nom du média *',
                'attr' => [
                    'placeholder' => 'Ex: RFI, Radio Maendeleo, CongoWeb',
                    'class' => 'modern-input'
                ]
            ])
            ->add('datePublication', DateType::class, [
                'label' => 'Date de publication',
                'required' => false,
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd/MM/yyyy',
                'attr' => [
                    'placeholder' => 'JJ/MM/AAAA',
                    'class' => 'modern-datepicker',
                    'data-date-format' => 'DD/MM/YYYY'
                ]
            ])
            ->add('lien', UrlType::class, [
                'label' => 'Lien vers l\'article *',
                'attr' => [
                    'placeholder' => 'https://www.exemple.com/article',
                    'class' => 'modern-input'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description (résumé)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Résumé de l\'article ou description',
                    'class' => 'modern-textarea',
                    'rows' => 4
                ]
            ])
            ->add('logoMedia', TextType::class, [
                'label' => 'Logo du média (URL)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'https://www.exemple.com/logo.png',
                    'class' => 'modern-input'
                ],
                'help' => 'URL vers le logo du média si disponible'
            ])
            ->add('typeMedia', TextType::class, [
                'label' => 'Type de média',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ex: Journal, Radio, Télévision, Site Web',
                    'class' => 'modern-input'
                ]
            ])
            ->add('actif', CheckboxType::class, [
                'label' => 'Actif',
                'required' => false,
                'attr' => [
                    'class' => 'modern-checkbox'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RelationMedia::class,
        ]);
    }
}
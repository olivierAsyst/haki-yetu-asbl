<?php

namespace App\Form;

use App\Entity\Partenaire;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class PartenaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom du partenaire',
                'attr' => [
                    'placeholder' => 'Ex: Organisation Mondiale de la Santé',
                    'class' => 'input-modern',
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Description du partenaire et de sa collaboration avec HAKI YETU',
                    'rows' => 4,
                    'class' => 'input-modern',
                ],
            ])
            ->add('url', UrlType::class, [
                'label' => 'Site web',
                'required' => false,
                'attr' => [
                    'placeholder' => 'https://www.exemple.org',
                    'class' => 'input-modern',
                ],
            ])
            ->add('type', ChoiceType::class, [
                'label' => 'Type de partenaire',
                'choices' => [
                    'Institutionnel' => 'institutionnel',
                    'ONG Internationale' => 'ong_internationale',
                    'ONG Nationale' => 'ong_nationale',
                    'Organisme public' => 'organisme_public',
                    'Entreprise privée' => 'entreprise_privee',
                    'Bailleur de fonds' => 'bailleur_fonds',
                    'Partenaire technique' => 'partenaire_technique',
                    'Autre' => 'autre',
                ],
                'placeholder' => 'Sélectionnez un type',
                'required' => false,
                'attr' => [
                    'class' => 'input-modern',
                ],
            ])
            ->add('categorie', ChoiceType::class, [
                'label' => 'Catégorie',
                'choices' => [
                    'Principaux partenaires' => 'principaux',
                    'Partenaires techniques' => 'techniques',
                    'Bailleurs de fonds' => 'bailleurs',
                    'Soutiens locaux' => 'locaux',
                    'Collaborateurs' => 'collaborateurs',
                ],
                'placeholder' => 'Sélectionnez une catégorie',
                'required' => false,
                'attr' => [
                    'class' => 'input-modern',
                ],
            ])
            ->add('logoFile', VichFileType::class, [
                'label' => 'Logo du partenaire',
                'required' => false,
                'allow_delete' => true,
                'download_label' => 'Télécharger',
                'download_uri' => true,
                'asset_helper' => true,
                'attr' => [
                    'class' => 'input-modern',
                ],
            ])
            ->add('ordreAffichage', NumberType::class, [
                'label' => 'Ordre d\'affichage',
                'required' => false,
                'html5' => true,
                'attr' => [
                    'placeholder' => '0, 1, 2, ...',
                    'min' => 0,
                    'class' => 'input-modern',
                ],
                'help' => 'Plus le nombre est petit, plus le partenaire apparaît en premier (laisser vide pour afficher par ordre alphabétique)',
            ])
            ->add('actif', CheckboxType::class, [
                'label' => 'Actif',
                'required' => false,
                'attr' => [
                    'class' => 'rounded border-navy-300 text-brand-700 focus:ring-brand-700',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Partenaire::class,
        ]);
    }
}
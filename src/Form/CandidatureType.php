<?php

namespace App\Form;

use App\Entity\Candidature;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class CandidatureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom *',
                'attr' => ['class' => 'w-full px-4 py-3 rounded-lg border border-navy-200 focus:border-brand-700 focus:ring-2 focus:ring-brand-700/20 outline-none transition'],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom *',
                'attr' => ['class' => 'w-full px-4 py-3 rounded-lg border border-navy-200 focus:border-brand-700 focus:ring-2 focus:ring-brand-700/20 outline-none transition'],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email *',
                'attr' => ['class' => 'w-full px-4 py-3 rounded-lg border border-navy-200 focus:border-brand-700 focus:ring-2 focus:ring-brand-700/20 outline-none transition'],
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone *',
                'attr' => ['class' => 'w-full px-4 py-3 rounded-lg border border-navy-200 focus:border-brand-700 focus:ring-2 focus:ring-brand-700/20 outline-none transition'],
            ])
            ->add('adresse', TextareaType::class, [
                'label' => 'Adresse',
                'required' => false,
                'attr' => ['class' => 'w-full px-4 py-3 rounded-lg border border-navy-200 focus:border-brand-700 focus:ring-2 focus:ring-brand-700/20 outline-none transition', 'rows' => 3],
            ])
            ->add('cvFileFile', VichFileType::class, [
                'label' => 'CV *',
                'required' => true,
                'attr' => ['class' => 'w-full px-4 py-3 rounded-lg border border-navy-200 focus:border-brand-700 focus:ring-2 focus:ring-brand-700/20 outline-none transition'],
            ])
            ->add('lettreMotivationFileFile', VichFileType::class, [
                'label' => 'Lettre de motivation',
                'required' => false,
                'attr' => ['class' => 'w-full px-4 py-3 rounded-lg border border-navy-200 focus:border-brand-700 focus:ring-2 focus:ring-brand-700/20 outline-none transition'],
            ])
            ->add('autresDocumentsFile', VichFileType::class, [
                'label' => 'Autres documents (PDF ou ZIP)',
                'required' => false,
                'attr' => ['class' => 'w-full px-4 py-3 rounded-lg border border-navy-200 focus:border-brand-700 focus:ring-2 focus:ring-brand-700/20 outline-none transition'],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message',
                'required' => false,
                'attr' => ['class' => 'w-full px-4 py-3 rounded-lg border border-navy-200 focus:border-brand-700 focus:ring-2 focus:ring-brand-700/20 outline-none transition', 'rows' => 4, 'placeholder' => 'Ajoutez un message supplémentaire si nécessaire...'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Candidature::class,
        ]);
    }
}

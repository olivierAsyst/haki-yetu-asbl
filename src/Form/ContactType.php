<?php

namespace App\Form;

use App\Entity\Message;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $inputClass = 'w-full rounded-lg border border-stone-300 bg-white px-4 py-3 text-stone-900 placeholder:text-stone-400 focus:border-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-700/30 transition';

        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom complet',
                'attr' => ['class' => $inputClass, 'placeholder' => 'Votre nom et prénom'],
                'constraints' => [new NotBlank(message: 'Merci de renseigner votre nom.')],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail',
                'attr' => ['class' => $inputClass, 'placeholder' => 'vous@exemple.com'],
                'constraints' => [new NotBlank(message: 'Merci de renseigner votre e-mail.')],
            ])
            ->add('sujet', TextType::class, [
                'label' => 'Sujet',
                'required' => false,
                'attr' => ['class' => $inputClass, 'placeholder' => 'Objet de votre message (facultatif)'],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Votre message',
                'attr' => ['class' => $inputClass, 'rows' => 6, 'placeholder' => 'Écrivez votre message ici...'],
                'constraints' => [
                    new NotBlank(message: 'Le message ne peut pas être vide.'),
                    new Length(min: 10, minMessage: 'Votre message est un peu court, dites-nous en un peu plus.'),
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Message::class,
        ]);
    }
}

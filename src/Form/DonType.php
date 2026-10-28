<?php

namespace App\Form;

use App\Entity\Don;
use App\Entity\Urgence;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // Étape 1: Informations personnelles
            ->add('typePersonne', ChoiceType::class, [
                'label' => 'Je suis un(e)',
                'choices' => [
                    'Particulier' => 'particulier',
                    'Entreprise/Organisation' => 'entreprise',
                ],
                'expanded' => true,
                'multiple' => false,
                'attr' => [
                    'class' => 'space-y-2',
                ],
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom et prénom',
                'attr' => [
                    'placeholder' => 'Ex: Jean Dupont',
                    'class' => 'input-modern',
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email',
                'attr' => [
                    'placeholder' => 'exemple@email.com',
                    'class' => 'input-modern',
                ],
            ])
            ->add('telephone', TextType::class, [
                'label' => 'Téléphone',
                'required' => false,
                'attr' => [
                    'placeholder' => '+243 99 123 4567',
                    'class' => 'input-modern',
                ],
            ])
            ->add('entreprise', TextType::class, [
                'label' => 'Nom de l\'entreprise/organisation',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ex: Société ABC',
                    'class' => 'input-modern',
                ],
            ])
            ->add('adresse', TextType::class, [
                'label' => 'Adresse',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ex: Av. du Congo, N°68',
                    'class' => 'input-modern',
                ],
            ])
            ->add('ville', TextType::class, [
                'label' => 'Ville',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ex: Uvira',
                    'class' => 'input-modern',
                ],
            ])
            ->add('pays', TextType::class, [
                'label' => 'Pays',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Ex: RD Congo',
                    'class' => 'input-modern',
                ],
            ])

            // Étape 2: Montant et type de don
            ->add('typeDon', ChoiceType::class, [
                'label' => 'Type de don',
                'choices' => [
                    'Don unique' => 'unique',
                    'Don mensuel' => 'mensuel',
                    'Don trimestriel' => 'trimestriel',
                    'Don annuel' => 'annuel',
                    'Don pour un projet spécifique' => 'projet',
                    'Don d\'urgence' => 'urgence',
                ],
                'placeholder' => 'Sélectionnez un type',
                'attr' => [
                    'class' => 'input-modern',
                ],
            ])
            ->add('montant', MoneyType::class, [
                'label' => 'Montant du don',
                // Pas de devise fixe ici : elle est choisie dans le champ « Devise »
                'currency' => false,
                'input' => 'string',
                'scale' => 2,
                'attr' => [
                    'placeholder' => '50.00',
                    'min' => 1,
                    'step' => 0.01,
                    'class' => 'input-modern',
                ],
                'help' => 'Dans la devise choisie ci-dessous',
            ])
            ->add('devise', ChoiceType::class, [
                'label' => 'Devise',
                'choices' => [
                    'USD ($)' => 'USD',
                    'EUR (€)' => 'EUR',
                    'CDF (FC)' => 'CDF',
                ],
                'attr' => [
                    'class' => 'input-modern',
                ],
            ])

            // Étape 3: Méthode de paiement
            ->add('methodePaiement', ChoiceType::class, [
                'label' => 'Méthode de paiement',
                'choices' => [
                    'Mobile Money (M-Pesa, Airtel Money, Orange Money)' => 'mobile_money',
                    'Virement bancaire' => 'virement_bancaire',
                    'Wave' => 'wave',
                    'Western Union' => 'western_union',
                    'MoneyGram' => 'moneygram',
                    'Espèces' => 'especes',
                    'Autre' => 'autre',
                ],
                'placeholder' => 'Sélectionnez une méthode',
                'attr' => [
                    'class' => 'input-modern',
                ],
            ])

            // Étape 4: Options supplémentaires
            ->add('message', TextareaType::class, [
                'label' => 'Message (optionnel)',
                'required' => false,
                'attr' => [
                    'placeholder' => 'Un message pour l\'équipe de HAKI YETU...',
                    'rows' => 4,
                    'class' => 'input-modern',
                ],
            ])
            ->add('anonyme', CheckboxType::class, [
                'label' => 'Je souhaite rester anonyme',
                'required' => false,
                'attr' => [
                    'class' => 'rounded border-navy-300 text-brand-700 focus:ring-brand-700',
                ],
            ])
            ->add('receiptDemande', CheckboxType::class, [
                'label' => 'Je souhaite recevoir un reçu fiscal',
                'required' => false,
                'attr' => [
                    'class' => 'rounded border-navy-300 text-brand-700 focus:ring-brand-700',
                ],
            ])
            ->add('recevoirNewsletter', CheckboxType::class, [
                'label' => 'Je souhaite recevoir la newsletter de HAKI YETU',
                'required' => false,
                'attr' => [
                    'class' => 'rounded border-navy-300 text-brand-700 focus:ring-brand-700',
                ],
            ])
        ;

        if (!$options['admin']) {
            $builder->add('site_internet', HoneypotType::class);
        }

        // Champs de suivi réservés à l'administration (jamais dans le formulaire public)
        if ($options['admin']) {
            $builder
                ->add('statut', ChoiceType::class, [
                    'label' => 'Statut',
                    'choices' => [
                        'En attente' => 'en_attente',
                        'Confirmé' => 'confirme',
                        'Payé' => 'paye',
                        'Annulé' => 'annule',
                        'Remboursé' => 'rembourse',
                    ],
                    'attr' => ['class' => 'input-modern'],
                ])
                ->add('urgence', EntityType::class, [
                    'label' => 'Appel d\'urgence lié',
                    'class' => Urgence::class,
                    'choice_label' => 'titre',
                    'required' => false,
                    'placeholder' => 'Aucun (don général)',
                    'help' => 'Si le don est payé en CDF, il est ajouté automatiquement au montant collecté de l\'urgence.',
                    'attr' => ['class' => 'input-modern'],
                ])
                ->add('referencePaiement', TextType::class, [
                    'label' => 'Référence du paiement',
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'Ex: n° de transaction Mobile Money',
                        'class' => 'input-modern',
                    ],
                ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Don::class,
            'admin' => false,
        ]);
        $resolver->setAllowedTypes('admin', 'bool');
    }
}
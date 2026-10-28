<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Blank;

/**
 * Champ piège anti-spam : invisible pour les visiteurs (et ignoré par les lecteurs d'écran),
 * mais les robots le remplissent. S'il contient quelque chose, l'envoi est refusé.
 */
class HoneypotType extends AbstractType
{
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'mapped' => false,
            'required' => false,
            'label' => 'Laissez ce champ vide',
            'constraints' => [new Blank(message: 'Votre envoi a été bloqué par le filtre anti-spam.')],
            'attr' => [
                'autocomplete' => 'off',
                'tabindex' => '-1',
            ],
            'row_attr' => [
                'aria-hidden' => 'true',
                'style' => 'position:absolute;left:-10000px;top:auto;width:1px;height:1px;overflow:hidden;',
            ],
        ]);
    }

    public function getParent(): string
    {
        return TextType::class;
    }
}

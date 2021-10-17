<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Validator\Constraints\NotBlank;

class ChercherArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mots',SearchType::class,[
                 'constraints' => new NotBlank([
                    'message' => 'Veillez renseigner votre mot clés'
                ]),
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'id' => 'inputModalSearch',
                    'placeholder' => 'Chercher ...'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}

<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Vich\UploaderBundle\Form\Type\VichImageType;

class UserEditFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
             ->add('imageFile', VichImageType::class, [
                'label' => 'Image (JPG or PNG file)',
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'Delete',
                'download_uri' => true,
                'imagine_pattern' => 'squared_thumbnail_small',
                'constraints' => [
                        new Image(['maxSize' => '8M'])
                ]
        ])
            ->add('firstName')
            ->add('lastName')
            ->add('phone')
            ->add('adresse')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

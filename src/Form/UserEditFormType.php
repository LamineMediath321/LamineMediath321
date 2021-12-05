<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Image;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class UserEditFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
             ->add('imageFile', VichImageType::class, [
                'label' => 'Image de profil (JPG or PNG file)',
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'Delete',
                'download_uri' => false,
                'imagine_pattern' => 'squared_thumbnail_small',
                'constraints' => [
                        new Image(['maxSize' => '8M'])
                ]
        ])
            ->add('firstName',TextType::class,[
                'label' => 'Prenom'
            ])
            ->add('lastName',TextType::class,[
                'label' => 'Nom'
            ])
            ->add('phone',TextType::class,[
                'label' => 'Téléphone'
            ])
            ->add('adresse')
            ->add('aboutMe',TextType::class,[
                'label' => 'A propos de moi'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

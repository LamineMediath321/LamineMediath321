<?php

namespace App\Form;

use App\Entity\Store;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Image;
use Symfony\Component\Validator\Constraints\NotBlank;
use Vich\UploaderBundle\Form\Type\VichImageType;
use App\Entity\SousCategorie;
use App\Repository\SousCategorieRepository;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class StoreType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('domaine',EntityType::class,[
                'placeholder' => 'Choisissez un domaine',
                'disabled' => false,
                'class' => SousCategorie::class,
                'choice_label' => 'nomCategorie',
                'query_builder' => function(SousCategorieRepository $sousCateRepo){
                    return $sousCateRepo->createQueryBuilder('s')->orderBy('s.nomCategorie','ASC');
                },
                 'constraints' => new NotBlank(['message' => 'Please choisissez un domaine']) 
            ])
            ->add('nomStore',TextType::class,[
                'constraints' => new NotBlank(['message' => 'Veillez donner un nom à votre store'])
            ])
            ->add('description')
            ->add('adresseStore',TextareaType::class,[
                'label' => 'Adresse du boutique (store)',
            ])
            ->add('imageFile', VichImageType::class, [
                'label' => 'Votre logo (JPG or PNG file)',
                'required' => true,
                'allow_delete' => true,
                'delete_label' => 'Delete',
                'download_uri' => true,
                'imagine_pattern' => 'squared_thumbnail_small',
                'constraints' => [
                        new Image(['maxSize' => '8M'])
                ]
            ])
            
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Store::class,
        ]);
    }
}

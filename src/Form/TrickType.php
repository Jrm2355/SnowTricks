<?php

namespace App\Form;

use App\Entity\Trick;
use App\Entity\Media;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TrickType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'Nom du trick'])
            ->add('description', TextareaType::class, ['label' => 'Description'])
            ->add('category')
            ->add('media', FileType::class,[ 'label' => 'Charger vos photos jpg', 'multiple' => true, 'mapped' => false, 'required' => true ])
            ->add('mediaVideo', TextAreaType::class,[ 'label' => 'Donnez le lien url de la video. Une vidéo par ligne', 'mapped' => false, 'required' => false ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Trick::class,
        ]);
    }
}

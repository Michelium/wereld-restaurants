<?php

namespace App\Form;

use App\Entity\Country;
use App\Entity\Restaurant;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RestaurantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('latitude')
            ->add('longitude')
            ->add('street')
            ->add('houseNumber')
            ->add('postalCode')
            ->add('city')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('visible')
            ->add('country', EntityType::class, [
                'class' => Country::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Restaurant::class,
        ]);
    }
}

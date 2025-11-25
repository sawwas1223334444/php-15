<?php

namespace App\Form;

use App\Entity\Client;
use App\Entity\Dish;
use App\Entity\Order;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('createdAt')
            ->add('client', EntityType::class, [
                'class' => Client::class,
                'choice_label' => 'id',
            ])
            ->add('dishes', EntityType::class, [
                'class' => Dish::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}

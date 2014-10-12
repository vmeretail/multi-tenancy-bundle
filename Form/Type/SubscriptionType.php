<?php

namespace Tahoe\Bundle\MultiTenancyBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class SubscriptionType extends AbstractType
{

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('first_name', 'text')
            ->add('last_name', 'text')
            ->add('credit_card_number', 'text')
            ->add('month', 'integer')
            ->add('year', 'integer')
        ;

    }

    public function getName()
    {
        return '';
    }
} 
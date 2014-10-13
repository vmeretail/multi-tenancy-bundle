<?php

namespace Tahoe\Bundle\MultiTenancyBundle\Form\Type;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

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
        ;
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function(FormEvent $event) {
            $data = $event->getData();
            $form = $event->getForm();

            $credit_card = null;
            $expiration_date = null;
            if (null !== $data) {
                $credit_card = sprintf("%s******%s", $data->first_six, $data->last_four);
                $expiration_date = sprintf("%s / %s", $data->month, $data->year);
            }
            $form
                ->add('credit_card_number', 'text', array(
                'data' => $credit_card
                ))
                ->add('expiration', 'text', array(
                    "data" => $expiration_date,
                    'attr' => array(
                        "placeholder" => "mm / yyyy"
                    )
                ))
            ;
        });
//            ->add('credit_card_number', 'text')
//            ->add('expiration', 'text', array(
//                'attr' => array(
//                    "placeholder" => "mm / yyyy"
//                )
//            ))
        ;

    }

    public function getName()
    {
        return 'tahoe_multitenancy_subscription';
    }
} 
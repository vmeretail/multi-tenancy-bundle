<?php

namespace Tahoe\Bundle\MultiTenancyBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;

class RegistrationFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        // add your custom field
        $builder->add('tenantName', 'text', array('mapped' => false));
        $builder->add('tenantSubdomain', 'text', array('label' => 'subdomain', 'mapped' => false));

    }

    public function getName()
    {
        return 'tahoe_multitenancy_user_registration';
    }
}
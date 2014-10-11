<?php


namespace Tahoe\Bundle\MultiTenancyBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Tahoe\Bundle\MultiTenancyBundle\Form\Type\UserActionsType;

class UserListType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('promote', 'text', array(

                ))
            ->add('demote', 'text', array(

                ))
            ->add('remove', 'text', array(

                ))
        ;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'user_list_form';
    }
}
<?php
/**
 * Created by PhpStorm.
 * User: steva
 * Date: 15.12.17.
 * Time: 08.45
 */

namespace SalexUserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ProfileFormType extends AbstractType
{
    public function buildUserForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('pokemon');
    }

    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\ProfileFormType';
    }

    public function getBlockPrefix()
    {
        return 'salex_user_profile';
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

}
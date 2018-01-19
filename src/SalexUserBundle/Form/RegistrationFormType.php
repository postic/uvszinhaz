<?php
/**
 * Created by PhpStorm.
 * User: steva
 * Date: 14.12.17.
 * Time: 13.27
 */

namespace SalexUserBundle\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)

    {
        $builder->add('phone_number', TextType::class, array(
            'label' => false,
            'attr' => array(
                'placeholder' => 'Broj telefona',
            )
        ));
    }

    public function getParent()

    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }

    public function getBlockPrefix()

    {
        return 'salex_user_registration';
    }

    public function getName()

    {
        return $this->getBlockPrefix();
    }

}
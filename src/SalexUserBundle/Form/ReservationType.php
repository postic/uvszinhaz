<?php

namespace SalexUserBundle\Form;

use SalexUserBundle\Utility\Services;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationType extends AbstractType
{

    protected $service;

    public function __construct(Services $service) {
        $this->service = $service;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = $this->service->getPerformances();

        $builder
            ->add('performance_id', ChoiceType::class, array('choices' => $choices,'label' => 'Performance'))
            ->add('type', ChoiceType::class, array('choices' => array('Pojedinačno' => 1, 'Grupe (najmanje 5 gledalaca)' => 2, 'Studenti/đaci' => 3, 'Penzionerska' => 4),'label' => 'Type'))
            ->add('seats_number', IntegerType::class, array('label' => 'Seats'))
            ->add('save', SubmitType::class, array('label' => 'Create Reservation'));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'SalexUserBundle\Entity\Reservation'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'salexuserbundle_reservation';
    }


}

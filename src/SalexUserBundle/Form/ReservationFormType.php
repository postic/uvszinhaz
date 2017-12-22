<?php

namespace SalexUserBundle\Form;

use SalexUserBundle\Utility\Services;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ReservationFormType extends AbstractType
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

        //$data = $options['data'];
        //$user = $data->getUser();

        $builder->add(
            'firstName',
            TextType::class,
            array(
                'label' => false,
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Ime'
                )
            )
        );
        $builder->add(
            'lastName',
            TextType::class,
            array(
                'label' => false,
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Prezime'
                )
            )
        );
        $builder->add(
            'phoneNumber',
            TextType::class,
            array(
                'label' => false,
                'required' => true,
                'attr' => array(
                    'placeholder' => 'Broj telefona'
                )
            )
        );
        $builder->add(
            'performanceId',
            ChoiceType::class,
            array(
                'choices' => $choices,
                'label' => false,
                'placeholder' => 'Izaberite predstavu',
                'required' => true
            )
        );
        $builder->add(
            'type',
            ChoiceType::class,
            array(
                'choices' => array('Pojedinačno' => 1, 'Grupe (najmanje 5 gledalaca)' => 2, 'Studenti/đaci' => 3, 'Penzionerska' => 4),
                'label' => false,
                'placeholder' => 'Izaberite tip karte',
                'required' => true
            )
        );
        $builder->add(
            'seatsNumber',
            ChoiceType::class,
            array(
                'choices' => array(1,2,3,4,5,6,7,8,9,10),
                'label' => false,
                'placeholder' => 'Izaberite broj karata'
            )
        );
        $builder->add(
            'save',
            SubmitType::class,
            array(
                'label' => 'Kreiraj zahtev'
            )
        );
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

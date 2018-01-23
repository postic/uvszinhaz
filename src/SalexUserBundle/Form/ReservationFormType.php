<?php

namespace SalexUserBundle\Form;

use SalexUserBundle\Utility\Services;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
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

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {

            $choices = $this->service->getPerformances();

            $data = $event->getData();
            $byPhone = $data->getByPhone();
            $form = $event->getForm();

            $user = $data->getUser();

            // Rezervacija preko sajta
            if($byPhone == 0) {

                $form->add(
                    'performanceId',
                    ChoiceType::class,
                    array(
                        'choices' => $choices,
                        'label' => false,
                        'placeholder' => 'Izaberite predstavu',
                        'required' => true
                    )
                );

                $form->add(
                    'scena',
                    HiddenType::class
                );

                $form->add(
                    'brojPojedinacne',
                    IntegerType::class,
                    array(
                        'label' => false,
                        'attr' => array(
                            'placeholder' => 'Broj pojedinačnih karata',
                            'min' => 1,
                            'max' => 10
                        ),
                        'required' => false
                    )
                );

                $form->add(
                    'brojGrupne',
                    IntegerType::class,
                    array(
                        'label' => false,
                        'attr' => array(
                            'placeholder' => 'Broj grupnih karata',
                            'min' => 1,
                            'max' => 10
                        ),
                        'required' => false
                    )
                );

                $form->add(
                    'brojStudentske',
                    IntegerType::class,
                    array(
                        'label' => false,
                        'attr' => array(
                            'placeholder' => 'Broj studentskih karata',
                            'min' => 1,
                            'max' => 10
                        ),
                        'required' => false
                    )
                );

                $form->add(
                    'brojPenzionerske',
                    IntegerType::class,
                    array(
                        'label' => false,
                        'attr' => array(
                            'placeholder' => 'Broj penzionerskih karata',
                            'min' => 1,
                            'max' => 10
                        ),
                        'required' => false
                    )
                );

                $form->add(
                    'save',
                    SubmitType::class,
                    array(
                        'label' => 'Kreiraj zahtev'
                    )
                );

            }

            // Rezervacija telefonom
            if($byPhone == 1) {

                $form->add(
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

                $form->add(
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

                $form->add(
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

                $form->add(
                    'performanceId',
                    ChoiceType::class,
                    array(
                        'choices' => $choices,
                        'label' => false,
                        'placeholder' => 'Izaberite predstavu',
                        'required' => true
                    )
                );

                $form->add(
                    'scena',
                    HiddenType::class
                );

                $form->add(
                    'brojPojedinacne',
                    IntegerType::class,
                    array(
                        'label' => false,
                        'attr' => array(
                            'placeholder' => 'Broj pojedinačnih karata',
                            'min' => 1,
                            'max' => 10
                        ),
                        'required' => false
                    )
                );

                $form->add(
                    'brojGrupne',
                    IntegerType::class,
                    array(
                        'label' => false,
                        'attr' => array(
                            'placeholder' => 'Broj grupnih karata',
                            'min' => 1,
                            'max' => 10
                        ),
                        'required' => false
                    )
                );

                $form->add(
                    'brojStudentske',
                    IntegerType::class,
                    array(
                        'label' => false,
                        'attr' => array(
                            'placeholder' => 'Broj studentskih karata',
                            'min' => 1,
                            'max' => 10
                        ),
                        'required' => false
                    )
                );

                $form->add(
                    'brojPenzionerske',
                    IntegerType::class,
                    array(
                        'label' => false,
                        'attr' => array(
                            'placeholder' => 'Broj penzionerskih karata',
                            'min' => 1,
                            'max' => 10
                        ),
                        'required' => false
                    )
                );

                $form->add(
                    'save',
                    SubmitType::class,
                    array(
                        'label' => 'Kreiraj zahtev'
                    )
                );
            }

        });

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

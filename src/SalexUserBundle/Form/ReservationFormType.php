<?php

namespace SalexUserBundle\Form;

use Doctrine\ORM\EntityManagerInterface;
use SalexUserBundle\Entity\Performance;
use SalexUserBundle\Utility\Services;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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

    protected $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {

            $now = new \DateTime();
            $current_date = $now->format('Y-m-d');

            $choices = array();
            $qb = $this->em
                ->getRepository(Performance::class)
                ->createQueryBuilder('p');
            $qb->where($qb->expr()->gte('p.date', ':current_date'));
            $qb->setParameter('current_date', $current_date);

            $items = $qb->getQuery()->getResult();
            foreach ($items as $item){
               $choices[$item->getTitle() . ' - ' . $item->getDate()->format('d.m.Y.')] = $item->getId();
            }

            $data = $event->getData();
            $byPhone = $data->getByPhone();
            $form = $event->getForm();
            $user = $data->getUser();
            // Rezervacija preko sajta
            if($byPhone == 0) {

                $form->add(
                    'performance',
                    ChoiceType::class,
                    array(
                        'choices'  => $items,
                        'label' => false,
                        'placeholder' => 'Izaberite predstavu',
                        'choice_label' => function ($item) {
                            return $item->getTitle() . ' - ' . $item->getDate()->format('d.m.Y.');
                        },
                        'choice_value' => function (Performance $item = null) {
                            return $item ? $item->getId() : '';
                        },

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
                    'performance',
                    ChoiceType::class,
                    array(
                        'choices'  => $items,
                        'label' => false,
                        'placeholder' => 'Izaberite predstavu',
                        'choice_label' => function ($item) {
                            return $item->getTitle() . ' - ' . $item->getDate()->format('d.m.Y.');
                        },
                        'choice_value' => function (Performance $item = null) {
                            return $item ? $item->getId() : '';
                        },

                    )
                );

                $form->add(
                    'scena',
                    HiddenType::class
                );

                $form->add(
                    'brojBesplatne',
                    IntegerType::class,
                    array(
                        'label' => false,
                        'attr' => array(
                            'placeholder' => 'Broj besplatnih karata',
                            'min' => 1,
                            'max' => 10
                        ),
                        'required' => false
                    )
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

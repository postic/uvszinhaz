<?php

/**
 * Created by PhpStorm.
 * User: stevan
 * Date: 17.9.17.
 * Time: 09.39
 */
namespace SalexUserBundle\Filter;

use Doctrine\ORM\EntityManagerInterface;
use SalexUserBundle\Entity\Performance;
use SalexUserBundle\Utility\Services;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;

class TicketFilterType extends AbstractType
{

    protected $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = array();
        $qb = $this->em
            ->getRepository(Performance::class)
            ->createQueryBuilder('p');

        $items = $qb->getQuery()->getResult();
        foreach ($items as $item){
            $choices[$item->getTitle() . ' - ' . $item->getDate()->format('d.m.Y.')] = $item->getId();
        }

        $builder->add(
            'performance',
            Filters\ChoiceFilterType::class,
            array(
                'choices' => $choices,
                'label' => false,
                'placeholder' => 'Izaberite predstavu'
            )
        );
    }

    public function getBlockPrefix()
    {
        return 'ticket_filter';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
            'validation_groups' => array('filtering') // avoid NotBlank() constraint-related message
        ));
    }
}


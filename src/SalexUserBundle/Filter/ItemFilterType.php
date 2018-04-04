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

class ItemFilterType extends AbstractType
{

    protected $em;

    public function __construct(EntityManagerInterface $entityManager) {
        $this->em = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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

        $builder->add(
            'performance',
            Filters\ChoiceFilterType::class,
            array(
                'choices' => $choices,
                'label' => false,
                'placeholder' => 'Izaberite predstavu'
            )
        );

        $builder->add(
            'statusId',
            Filters\ChoiceFilterType::class,
            array(
                'choices' => array('Zahtev' => 1, 'Rezervisano' => 2, 'Otkazano' => 3),
                'label' => false,
                'placeholder' => 'Izaberite status'
            )
        );
    }

    public function getBlockPrefix()
    {
        return 'item_filter';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection'   => false,
            'validation_groups' => array('filtering')
        ));
    }
}


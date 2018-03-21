<?php

/**
 * Created by PhpStorm.
 * User: stevan
 * Date: 17.9.17.
 * Time: 09.39
 */
namespace SalexUserBundle\Filter;

use SalexUserBundle\Utility\Services;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;

class TicketFilterType extends AbstractType
{

    protected $service;

    public function __construct(Services $service) {
        $this->service = $service;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = array();
        $items = $this->service->getPerformances();
        foreach ($items as $item){
            $choices[$item['title'] . ' - ' . $item['datum']] = $item['ID'];
        }

        $builder->add(
            'performanceId',
            Filters\ChoiceFilterType::class,
            array('choices' => $choices, 'label' => false, 'placeholder' => 'Izaberite predstavu')
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
            'validation_groups' => array('filtering') // avoid NotBlank() constraint-related message
        ));
    }
}


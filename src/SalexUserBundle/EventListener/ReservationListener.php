<?php
/**
 * Created by PhpStorm.
 * User: stevan
 * Date: 14.9.17.
 * Time: 08.40
 */
namespace SalexUserBundle\EventListener;

use SalexUserBundle\Utility\Services;
use Doctrine\ORM\Event\LifecycleEventArgs;

class ReservationListener
{

    private $service;

    public function __construct(Services $service) {
        $this->service = $service;
    }

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if(method_exists($entity, 'setService')) {
            $entity->setService($this->service);
        }
    }

    public function postPersist(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if(method_exists($entity, 'setService')) {
            $entity->setService($this->service);
        }
    }

}
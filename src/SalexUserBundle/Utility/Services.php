<?php

/**
 * Created by PhpStorm.
 * User: stevan
 * Date: 13.9.17.
 * Time: 11.54
 */

namespace SalexUserBundle\Utility;

use Buzz\Browser;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Services
{

    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getPerformances() {

        $retval = array();
        $buzz = $this->container->get('buzz');
        $response = $buzz->get('http://uvszinhaz.com/performances')->getContent();
        $items = json_decode($response, true);
        foreach($items as $key=>$value){
            $retval[$value] = $key;
        }
        return $retval;
    }


    public function getPerformance($id) {

        $retval = array();
        $buzz = $this->container->get('buzz');
        $response = $buzz->get('http://uvszinhaz.com/performance/'.$id)->getContent();
        $node = json_decode($response, true);
        return $node['title'];
    }


}
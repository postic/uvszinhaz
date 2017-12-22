<?php

/**
 * Created by PhpStorm.
 * User: stevan
 * Date: 13.9.17.
 * Time: 11.54
 */

namespace SalexUserBundle\Utility;

use Buzz;
use Symfony\Bundle\MonologBundle\MonologBundle;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Services
{

    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getPerformances() {
        $retval = null;
        try {
            $browser = new Buzz\Browser();
            $browser->getClient()->setTimeout(100);
            $response = $browser->get('http://uvszinhaz.com/performances')->getContent();
            $items = json_decode($response, true);
            foreach($items as $key=>$value){
                $retval[$value] = $key;
            }
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
        return $retval;
    }


    public function getPerformance($id) {
        $retval = null;
        try{
            $browser = new Buzz\Browser();
            $browser->getClient()->setTimeout(100);
            $response = $browser->get('http://uvszinhaz.com/performance/'.$id)->getContent();
            $node = json_decode($response, true);
            $retval = $node['title'];
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
        return $retval;
    }


}
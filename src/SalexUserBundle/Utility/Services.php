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
            $locale = $this->container->get('session')->get('_locale');
            $locale_param = ($locale=='hu')?'hu':'sh';
            $url = $this->container->getParameter('url_performances');
            $browser = new Buzz\Browser();
            $browser->getClient()->setTimeout(100);
            $response = $browser->get($url.'/'.$locale_param.'/all')->getContent();
            $items = json_decode($response, true);
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
        return $items;
    }


    public function getPerformance($id) {
        $retval = null;
        try{
            $locale = $this->container->get('session')->get('_locale');
            $locale_param = ($locale=='hu')?'hu':'sh';
            $url = $this->container->getParameter('url_performances');
            $browser = new Buzz\Browser();
            $browser->getClient()->setTimeout(100);
            $response = $browser->get($url.'/'.$locale_param.'/'.$id)->getContent();
            $item = json_decode($response, true);
            $retval = $item;
        } catch (\Exception $e) {
            error_log($e->getMessage());
        }
        return $retval;
    }

}
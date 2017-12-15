<?php

namespace SalexUserBundle\EventListener;

use Avanzu\AdminThemeBundle\Event\SidebarMenuEvent;
use Avanzu\AdminThemeBundle\Model\MenuItemModel;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

class SidebarListener
{

    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function onSetupMenu(SidebarMenuEvent $event)
    {
        $request = $event->getRequest();

        foreach ($this->getMenu($request) as $item) {
            $event->addItem($item);
        }

    }

    /**
     * Get the sidebar menu
     *
     * @param Request $request
     * @return mixed
     */
    protected function getMenu(Request $request)
    {
        $is_admin = $this->container->get('security.authorization_checker')->isGranted('ROLE_SUPER_ADMIN');

        $earg = array();

        if($is_admin){
            $rootItems = array(
                $users = new MenuItemModel('id-users', 'Users', 'list_users', $earg, 'fa fa-circle-o'),
                $reservations = new MenuItemModel('id-reservation', 'Reservations', null, $earg, 'fa fa-circle-o'),
                $reservations->addChild(new MenuItemModel('id-reservation', 'List reservations', 'list_reservations', $earg, 'fa fa-circle-thin')),
                $reservations->addChild(new MenuItemModel('id-add-reservations', 'Add reservation', 'add_reservation', $earg, 'fa fa-circle-thin')),
            );
        } else {
            $rootItems = array (
                $my_reservations = new MenuItemModel('id-my-reservations', 'Reservations', 'list_my_reservations', $earg, 'fa fa-circle-o'),
                $add_reservation = new MenuItemModel('id-add-reservations', 'Add reservation', 'add_reservation', $earg, 'fa fa-circle-o'),
            );
        }

        return $this->activateByRoute($request->get('_route'), $rootItems);

    }

    /**
     * Set current menu item to be active
     *
     * @param $route
     * @param $items
     * @return mixed
     */
    protected function activateByRoute($route, $items) {

        foreach($items as $item) { /** @var $item MenuItemModel */
            if($item->hasChildren()) {
                $this->activateByRoute($route, $item->getChildren());
            }
            else {
                if($item->getRoute() == $route) {
                    $item->setIsActive(true);
                }
            }
        }

        return $items;
    }


}
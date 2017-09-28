<?php
/**
 * Created by PhpStorm.
 * User: stevan
 * Date: 26.9.17.
 * Time: 13.21
 */

namespace SalexUserBundle\EventListener;

use Avanzu\AdminThemeBundle\Event\NotificationListEvent;
use Avanzu\AdminThemeBundle\Model\NotificationModel;

class NotificationListener
{

    public function onListNotifications(NotificationListEvent $event) {

        foreach($this->getNotifications() as $notification) {
            $event->addNotification($notification);
        }

    }

    protected function getNotifications() {
        // retrieve your Notification models/entities here
        $items = array(
            $users = new NotificationModel('3 new users joined today', '2', 'fa fa-users text-aqua'),
            $reservations = new NotificationModel('You have 5 new reservation', '3', 'fa fa-list text-red')
        );
        return $items;
    }

}
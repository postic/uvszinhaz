<?php
/**
 * Created by PhpStorm.
 * User: stevan
 * Date: 29.9.17.
 * Time: 09.07
 */
namespace SalexUserBundle\EventListener;
// ...

use Avanzu\AdminThemeBundle\Event\MessageListEvent;
use FOS\UserBundle\Model\UserInterface;
use SalexUserBundle\Entity\User;
use SalexUserBundle\Model\MessageModel;
use Symfony\Component\DependencyInjection\ContainerInterface;

class MessageListener {

    private $container;

    public function __construct(ContainerInterface $container) {
        $this->container = $container;
    }

    public function onListMessages(MessageListEvent $event) {

        foreach($this->getMessages() as $message) {
            $event->addMessage($message);
        }

    }

    protected function getMessages() {

        $userManager = $this->container->get('fos_user.user_manager');
        $from = $userManager->findUserBy(array('id'=>1));

        // retrieve your message models/entities here
        $items = array(
            new MessageModel($from, 'Created reservation', new \DateTime(), $from),
            new MessageModel($userManager->findUserBy(array('id'=>1)), 'Created reservation', new \DateTime(), $from),
            new MessageModel($userManager->findUserBy(array('id'=>1)), 'Created reservation', new \DateTime(), $from),
        );

        return $items;
        
    }

}
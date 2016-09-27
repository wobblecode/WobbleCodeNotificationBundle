<?php

namespace WobbleCode\NotificationBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\EventDispatcher\GenericEvent;
use Doctrine\ODM\MongoDB\DocumentManager;
use WobbleCode\NotificationBundle\Service\Notificator;

class NotificationSubscriber implements EventSubscriberInterface
{
    /**
     * Notificator
     *
     * @var Notificator
     */
    private $notificator;

    /**
     * DocumentManager
     *
     * @var DocumentManager
     */
    private $dm;

    public function __construct(DocumentManager $dm, Notificator $notificator)
    {
        $this->dm = $dm;
        $this->notificator = $notificator;
    }

    public static function getSubscribedEvents()
    {
        return array(
            'app.notification' => array('notify')
        );
    }

    /**
     * Notify to users that are suscbribed to the event
     */
    public function notify(GenericEvent $event)
    {
        $this->notificator->processEvent($event);
    }
}

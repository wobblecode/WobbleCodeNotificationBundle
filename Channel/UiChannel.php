<?php

namespace WobbleCode\NotificationBundle\Channel;

use Symfony\Component\EventDispatcher\GenericEvent;
use WobbleCode\NotificationBundle\Document\Event;
use WobbleCode\NotificationBundle\Document\Notification;
use WobbleCode\NotificationBundle\Manager\NotificationManager;

class UiChannel implements ChannelInterface
{

    protected $notificationManager;
    
    protected $twig;

    public function __construct(NotificationManager $notificationManager, \Twig_Environment $twig)
    {
        $this->notificationManager = $notificationManager;
        $this->twig = $twig;
    }

    /**
     * Notify by channel
     *
     * @param GenericEvent $event
     * @param Event $eventEntity
     * @param null $user
     */
    public function notify(GenericEvent $event, Event $eventEntity, $lang = "en", $user = null)
    {
        $event->setArgument('notifyUserNotified', $user);
        $event->setArgument('notifyLevel', $eventEntity->getLevelName());

        $rendered = $this->renderNotification($event, $eventEntity->getUiTemplate(), $lang);

        $this->createNotification(
            $user,
            $eventEntity,
            $rendered['title'],
            $rendered['description'],
            $eventEntity->getLevel(),
            $event->hasArgument('notifyInterfacePlacement') ? $event->getArgument('notifyInterfacePlacement') : null,
            $event->hasArgument('notifyActionRequired') ? $event->getArgument('notifyActionRequired') : false
        );
    }

    /**
     * Render Notification for Email
     *
     * @param GenericEvent $event
     * @param $template
     * @param string $lang
     *
     * @return array
     */
    public function renderNotification(GenericEvent $event, $template, $lang = 'en')
    {
        $template = str_replace('%lang%', $lang, $template);
        $template = $this->twig->loadTemplate($template);

        $rendered = [];

        $rendered['title'] = $template->renderBlock(
            'subject',
            array(
                'locale' => $lang,
                'event' => $event
            )
        );

        $rendered['description'] = $template->renderBlock(
            'message',
            array(
                'locale' => $lang,
                'event' => $event
            )
        );

        return $rendered;
    }
    
    public function processNotification($args)
    {
        
    }

    /**
     * Create a entity notification instance and persist in db if is internal
     */
    private function createNotification(
        $user,
        $event,
        $title,
        $description,
        $level = 100,
        $interfacePlacement = null,
        $actionRequired = false
    ) {
        $notification = new Notification();

        $notification->setUser($user);
        $notification->setEvent($event);
        $notification->setTitle($title);
        $notification->setDescription($description);
        $notification->setLevel($level);
        $notification->setInterfacePlacement($interfacePlacement);
        $notification->setActionRequired($actionRequired);

        if ($actionRequired) {
            $notification->setActionStatus('pending');
        }
        
        $this->notificationManager->save([$notification]);

        return $notification;
    }
    
}
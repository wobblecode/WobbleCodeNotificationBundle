<?php

namespace WobbleCode\NotificationBundle\Twig;

use WobbleCode\NotificationBundle\Document\Notification;

/**
* TODO add option for user and placement
*/
class NotificationExtension extends \Twig_Extension
{
    /**
     * @var NotificationManager
     */
    protected $nm;

    public function __construct($notificationManager)
    {
        $this->nm = $notificationManager;
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('notification_unread', array($this->nm, 'getUnread')),
            new \Twig_SimpleFunction('notification_unread_count', array($this->nm, 'getUnreadCount')),
            new \Twig_SimpleFunction('notification_unread_level', array($this->nm, 'getUnreadLevel')),
            new \Twig_SimpleFunction('notification_action_required', array($this, 'checkActionRequired')),
        );
    }

    /**
     * TODO Remove HTML WTF?
     */
    public function checkActionRequired(Notification $notification)
    {
        $actionButton = '';

        if ($notification->getActionRequired()) {
            $actionButton = '<button class="btn btn-default" id="notification_action_button_'
                .$notification->getId().'">'.$notification->getActionStatus().'</button>';
        }

        return $actionButton;
    }

    public function getName()
    {
        return 'notification_extension';
    }
}

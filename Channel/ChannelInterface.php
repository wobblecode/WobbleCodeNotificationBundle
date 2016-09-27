<?php

namespace WobbleCode\NotificationBundle\Channel;

use Symfony\Component\EventDispatcher\GenericEvent;
use WobbleCode\NotificationBundle\Document\Event;

interface ChannelInterface
{

    /**
     * Generate Notification
     *
     * @param GenericEvent $event
     * @param Event $eventEntity
     * @param string $lang
     * @param null $user
     */
    public function notify(GenericEvent $event, Event $eventEntity, $lang = "en", $user = null);

    /**
     * Render Notification
     *
     * @param GenericEvent $event
     * @param $template
     * @param string $lang
     *
     * @return array
     */
    public function renderNotification(GenericEvent $event, $template, $lang = "en");

    /**
     * Deliver notification
     *
     * @param $args
     * @return mixed
     */
    public function processNotification($args);
}
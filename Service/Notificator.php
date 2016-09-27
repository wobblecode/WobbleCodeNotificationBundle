<?php

namespace WobbleCode\NotificationBundle\Service;

use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\User\User;
use WobbleCode\NotificationBundle\Document\Event;
use WobbleCode\NotificationBundle\Document\Subscription;
use Symfony\Component\EventDispatcher\GenericEvent;
use WobbleCode\NotificationBundle\Model\Notify;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class Notificator
{
    protected $dm;

    protected $eventDispatcher;

    protected $notification;

    protected $subscriptionManager;

    /**
     * DI Container
     *
     * @var ContainerInterface
     */
    protected $container;

    /**
     * Set DI container$
     *
     * @param ContainerInterface $container
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function __construct(DocumentManager $dm, $eventDispatcher, $subscriptionManager)
    {
        $this->dm              = $dm;
        $this->eventDispatcher = $eventDispatcher;
        $this->subscriptionManager = $subscriptionManager;
    }

    public function notifyByObject(Notify $notify)
    {
        $eventArguments = array(
            'subject'                  => $notify->getEvent(),
            'title'                    => $notify->getSubject(),
            'description'              => $notify->getMessage(),
            'notifyLevel'              => $notify->getLevel(),
            'notifyForceChannels'      => $notify->getForceChannels(),
            'notifyOrganizations'      => $notify->getOrganizations(),
            'notifyUsers'              => $notify->getUsers(),
            'notifyLanguageFilter'     => $notify->getLanguage(),
            'notifyActionRequired'     => $notify->getActionRequired(),
            'notifyInterfacePlacement' => $notify->getInterfacePlacement()
        );

        $this->notify($notify->getEvent()->getKey(), $eventArguments);
    }

    /**
     * Notify Helper Create a generic event and dispatch the event
     *
     * @param string $key       Event key name
     * @param array  $arguments Arguments with Organization included
     *
     * @example of arguments
     *
     *     $arguments = [
     *         'notifyLanguage' => 'es',
     *         'notifyUserTrigger' => $user,
     *         'notifyUsers' => [],
     *         'notifyExternal' => 'notify@email.com',
     *         'data' => [
     *             'invitation' => $invitation
     *         ]
     *     ];
     *
     * @return GenericEvent Dispatched event
     */
    public function notify($key, $arguments)
    {
        $event = new GenericEvent(
            $key,
            $arguments
        );

        $this->eventDispatcher->dispatch($key, $event);

        return $event;
    }

    /**
     * Determine the default notification lanaguage for the event
     *
     * @param GenericEvent $event
     *
     * @return String ISO Language
     */
    public function getDefaultLanguage($event)
    {
        if ($event->hasArgument('notifyLanguage')) {
            return $event->getArgument('notifyLanguage');
        } elseif ($event->hasArgument('notifyUserTrigger')) {
            $user = $event->getArgument('notifyUserTrigger');
            return $user->getContact()->getLocale();
        }
    }

    public function checkExpressionRule($entityEvent, $event)
    {
        $rule = $entityEvent->getExpressionRule();

        if ($rule) {
            $language = new ExpressionLanguage();
            $expression = $language->evaluate(
                $rule,
                array(
                    'event' => $event,
                )
            );

            return $expression;
        }

        return true;
    }

    /**
     * Process and Event used for notifications
     *
     * TODO Decouple logic
     *
     * @param GenericEvent $event Event with notificaiton info
     *
     * @return void
     */
    public function processEvent(GenericEvent $event)
    {
        $eventEntity = $this->dm->getRepository('WobbleCodeNotificationBundle:Event')
                                ->findOneBy(['key' => $event->getSubject()]);

        /**
         * Check Expression
         */
        $expressionCheck = $this->checkExpressionRule($eventEntity, $event);
        if (!$expressionCheck) {
            return;
        }

        /**
         * Filter by Language
         */
        $filterLanguage = false;
        if ($event->hasArgument('notifyFilterLanguage')) {
            $filterLanguage = $event->getArgument('notifyFilterLanguage');
        }

        /**
         * Directly Notify these Organizations
         */
        if ($event->hasArgument('notifyOrganizations')) {
            $organizations = $event->getArgument('notifyOrganizations');
            $subscriptions = $this->subscriptionManager->findEnabledByOrganizations($eventEntity, $organizations);
            $this->processNotifications($event, $eventEntity, $subscriptions, $filterLanguage);
        }

        /**
         * Directly Notify these Users
         */
        if ($event->hasArgument('notifyUsers')) {
            $subscriptions = $this->subscriptionManager->findEnabledByUsers(
                $eventEntity,
                $event->getArgument('notifyUsers')
            );
            $this->processNotifications($event, $eventEntity, $subscriptions, $filterLanguage);
        }

        /**
         * Process External Notifications for users that doesn't belong to the
         * system
         */
        if ($event->hasArgument('notifyExternal')) {
            $lang = $this->getDefaultLanguage($event);

            foreach ($event->getArgument('notifyExternal') as $to) {

                $this->container->get('wobblecode_notification.channel.mail')->notify($event, $eventEntity, $lang, $to);
            }
        }
    }

    /**
     * @param GenericEvent   $event
     * @param Event          $eventEntity
     * @param Subscription[] $subscriptions
     * @param null           $filterLanguage
     *
     * @return bool
     */
    public function processNotifications(GenericEvent $event, Event $eventEntity, $subscriptions, $filterLanguage = null)
    {

        foreach ($subscriptions as $subscription) {
            $user = $subscription->getUser();
            $lang = $user->getContact()->getLocale();

            if ($filterLanguage && $lang != $filterLanguage) {
                return false;
            }

            $this->channelNotify($event, $eventEntity, $subscription, $lang, $user);
        }
    }
    
    private function channelNotify(GenericEvent $event, Event $eventEntity, Subscription $subscription, $lang, $user)
    {
        if (!$subscription->getEnabled()) {

            return;
        }

        //ui channel
        $this->container->get('wobblecode_notification.channel.ui')->notify($event, $eventEntity, $lang, $user);

        $notifyForceChannels = $event->hasArgument('notifyForceChannels') ?
            $event->getArgument('notifyForceChannels') : [];
        
        //external channels
        foreach ($eventEntity->getChannels() as $channel) {

            if ($subscription->getChannel($channel->getName()) == true ||
                in_array($channel->getName(), $notifyForceChannels)
            ) {
                $this->container->get($channel->getService())->notify($event, $eventEntity, $lang, $user);
            }

        }
    }

}

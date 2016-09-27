<?php

namespace WobbleCode\NotificationBundle\Channel;

use Instasent\ResqueBundle\Resque;
use Symfony\Component\EventDispatcher\GenericEvent;
use WobbleCode\NotificationBundle\Document\Event;
use WobbleCode\NotificationBundle\Job\NotificationJob;
use WobbleCode\UserBundle\Document\User;

class MailChannel implements ChannelInterface
{
    protected $resque;
    protected $twig;
    protected $mailer;
    protected $transport;

    public function __construct(
        Resque $resque,
        \Twig_Environment $twig,
        \Swift_Mailer $mailer,
        \Swift_Transport $transport
    ) {
        $this->resque = $resque;
        $this->twig = $twig;
        $this->mailer = $mailer;
        $this->transport = $transport;
    }

    /**
     * Enqueue mail notification
     *
     * @param GenericEvent $event
     * @param Event $eventEntity
     * @param string $lang
     * @param null $user
     */
    public function notify(GenericEvent $event, Event $eventEntity, $lang = "en", $user = null)
    {
        $from = $eventEntity->getFromEmail();
        $fromName = $eventEntity->getFromName();

        //external notifications
        $to = $user;
        if ($user instanceof User) {
            $to = $user->getEmail();
        }

        $bcc = $eventEntity->getBcc();
        
        if (!$from) {
            throw new \InvalidArgumentException('Expecting and email in from arg. Input was: 
            "'.$from. '" Maybe the Contact related to the event '.$event->getSubject().
                'doesn\'t have an email');
        }

        $rendered = $this->renderNotification($event, $eventEntity->getChannelTemplate("mail"), $lang);

        $job = new NotificationJob();
        $job->args = array(
            'headers' => array(
                'X-MC-Tags' => $event->getSubject()
            ),
            'contentType' => 'text/html',
            'from' => $from,
            'from_name' => $fromName,
            'to' => $to,
            'bcc'=> $bcc,
            'subject' => $rendered["title"],
            'description' => $rendered["description"],
            'channel_service' => 'wobblecode_notification.channel.mail',
        );

        $this->resque->enqueue($job);
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
        $message = \Swift_Message::newInstance()
            ->setContentType($args['contentType'])
            ->setFrom($args['from'], $args['from_name'])
            ->setTo($args['to'])
            ->setBcc($args['bcc'])
            ->setSubject($args['subject'])
            ->setBody($args['description']);

        if ($args['headers']) {
            $headers = $message->getHeaders();

            foreach ($headers as $k => $v) {
                $headers->addTextHeader($k, $v);
            }
        }

        $this->mailer->send($message);

        $transport = $this->mailer->getTransport();
        $spool = $transport->getSpool();
        $spool->flushQueue($this->transport);
    }
}
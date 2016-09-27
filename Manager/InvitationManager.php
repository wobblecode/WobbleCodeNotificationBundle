<?php

namespace WobbleCode\NotificationBundle\Manager;

use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Security\Core\SecurityContext;
use WobbleCode\ManagerBundle\Manager\GenericDocumentManager;
use WobbleCode\UserBundle\Document\Invitation;

class InvitationManager extends GenericDocumentManager
{
    public function prepareInvitation($invitation, $organization, $user)
    {
        $hash = sha1($invitation->getEmail().uniqid());

        $invitation->setHash($hash);
        $invitation->setFrom($user);
        $invitation->setOrganization($organization);
    }

    public function send($invitation, $user)
    {
        $this->save([$invitation]);

        $event = [
            'notifyLanguage' => $invitation->getLocale(),
            'notifyUserTrigger' => $user,
            'notifyExternal' => array($invitation->getEmail()),
            'data' => [
                'invitation' => $invitation
            ]
        ];

        $this->dispatch('user.invitation.created', $event);
    }

    public function revokeAndRemove(Invitation $invitation)
    {
        $to = $invitation->getTo();

        if ($to) {

            $qb = $this->dm->createQueryBuilder('WobbleCodeNotificationBundle:Notification');
            $qb->remove()->field('user.$id')->equals(new \MongoId($to->getId()));
            $qb->getQuery()->execute();

            $qb = $this->dm->createQueryBuilder('WobbleCodeNotificationBundle:Subscription');
            $qb->remove()->field('user.$id')->equals(new \MongoId($to->getId()));
            $qb->getQuery()->execute();

            $this->remove([$to]);
        }

        $this->remove([$invitation]);
    }
}

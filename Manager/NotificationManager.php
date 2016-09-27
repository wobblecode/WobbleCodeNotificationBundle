<?php

namespace WobbleCode\NotificationBundle\Manager;

use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use WobbleCode\ManagerBundle\Manager\GenericDocumentManager;
use WobbleCode\NotificationBundle\Document\Notification;

/**
 * TODO Refactorize
 */
class NotificationManager extends GenericDocumentManager
{
    /**
     * User
     */
    protected $user = null;

    /**
     * TokenStorage
     *
     * @var TokenStorage
     */
    protected $tokenStorage;

    /**
     * Maps level with style
     *
     * @var array
     */
    protected $levelUiMapping = array(
        100 => 'default',
        200 => 'info',
        250 => 'info',
        280 => 'success',
        300 => 'warning',
        400 => 'danger',
        500 => 'danger',
        550 => 'danger',
        600 => 'danger'
    );

    /**
     * Set security context
     *
     * @param TokenStorage $tokenStorage
     */
    public function setTokenStorage(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * set the user to work with
     *
     * @param User $use UserEntity
     *
     * @return self
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Load current logged user
     *
     * @return self
     */
    public function loadLoggedUser()
    {
        $this->setUser($this->tokenStorage->getToken()->getUser());

        return $this;
    }

    /**
     * Get all the unread notifications
     *
     * @param string $placement  context of notification
     *
     * @return integer @unreadCount  count of unread notifications by placement
     */
    public function getRead($user, $placement = false)
    {
        $qb = $this->dm->createQueryBuilder($this->document);

        $query = $qb->field('user.$id')->equals(new \MongoId($user->getId()))
                    ->field('readAt')->notEqual(null)
                    ->sort('createdAt', 'desc')
                    ->getQuery();

        return $query->execute();
    }

    /**
     * Get all the unread notifications
     *
     * @param string $placement context of notification
     *
     * @return integer @unreadCount count of unread notifications by placement
     */
    public function getUnread($user = null, $placement = false)
    {
        if (!$user) {
            $user = $this->user;
        }

        $qb = $this->dm->createQueryBuilder($this->document);

        $query = $qb->field('user.$id')->equals(new \MongoId($user->getId()))
                    ->field('readAt')->equals(null)
                    ->sort('createdAt', 'desc')
                    ->getQuery();

        if ($placement) {
            $qb->field('interfacePlacement')->equals($placement);
        }

        return $query->execute();
    }

    public function getUnreadCount($user, $placement = '')
    {
        $cursor = $this->getUnread($user, $placement);

        return $cursor->count();
    }

    /**
     * Get all the unread notifications
     *
     * @param string $placement  context of notification
     *
     * @return integer @unreadCount count of unread notifications by placement
     */
    public function getUnreadLevel($user, $placement = false)
    {
        $this->repo = $this->dm->getRepository($this->document);
        $levelPriority = $this->getMaxLevelPriorityByUser($user, $placement);

        if ($levelPriority == false) {
            $levelPriority = 100;
        }

        return $this->levelUiMapping[$levelPriority];
    }

    /**
     * Get Max leve of un read notifications
     *
     * @param User   $user
     * @param string $placement Optional placement
     *
     * @return integer
     */
    public function getMaxLevelPriorityByUser($user, $placement = '')
    {
        $qb = $this->dm->createQueryBuilder($this->document);

        $query = $qb->field('user.$id')->equals(new \MongoId($user->getId()))
                    ->field('readAt')->equals(null)
                    ->sort('level', 'desc')
                    ->limit(1)
                    ->getQuery();

        if ($placement) {
            $qb->field('interfacePlacement')->equals($placement);
        }

        $notification = $query->getSingleResult();

        if ($notification) {
            return $notification->getLevel();
        }

        return 0;
    }

    public function read(Notification $notification)
    {
        $notification->setStatus('read');
        $notification->setReadAt(new \Datetime());

        $this->save([$notification]);
    }

    public function task(Notification $notification)
    {
        $notification->setStatus('read');
        $notification->setReadAt(new \Datetime());

        $this->save([$notification]);
    }

    /**
     * Change action status
     *
     * @param Notification $notification
     *
     * @return self
     */
    public function toggleTask(Notification $notification)
    {
        $notification->setActionStatus('completed');
        $notification->setActionAt(new \DateTime());

        if ($notification->getActionStatus() == 'completed') {
            $notification->setActionStatus('pending');
            $notification->setActionAt(null);
        }

        $this->save([$notification]);

        return $this;
    }

    public function allRead($user)
    {
        $repo = $this->dm->getRepository($this->document);

        $notifications = $repo->findBy(array(
            'user.$id' => new \MongoId($user->getId()),
            'status' => 'unread'
        ));

        foreach ($notifications as $notification) {
            $isReadable = true;
            if ($notification->getActionRequired() && $notification->getActionStatus() == 'pending') {
                $isReadable = false;
            }

            if (!$notification->getActionRequired() || $isReadable) {
                $notification->setStatus('read');
                $notification->setReadAt(new \Datetime());
                $this->dm->persist($notification);
            }
        }

        $this->dm->flush();

        return $this;
    }
}

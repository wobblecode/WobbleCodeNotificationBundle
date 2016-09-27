<?php

namespace WobbleCode\NotificationBundle\Manager;

use Doctrine\ODM\MongoDB\DocumentManager;
use WobbleCode\UserBundle\Document\User;
use WobbleCode\UserBundle\Model\OrganizationInterface;
use WobbleCode\NotificationBundle\Document\Event;
use WobbleCode\NotificationBundle\Document\Subscription;
use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Symfony\Component\Security\Core\Role\Role;
use WobbleCode\ManagerBundle\Manager\GenericDocumentManager;

/**
* TODO complete Draft for Subscription Manager
*/
class SubscriptionManager extends GenericDocumentManager
{
    /**
     * Role Hierachy
     *
     * @var Array
     */
    protected $roles;

    /**
     * RoleHierarchy
     *
     * @var RoleHierarchy
     */
    protected $roleHierarchy;

    /**
     * Set Role hierachy
     *
     * @param RoleHierarchy $roleHierarchy
     *
     * @return self
     */
    public function setRoleHierarchy(RoleHierarchy $roleHierarchy)
    {
        $this->roleHierarchy = $roleHierarchy;

        return $this;
    }

    public function getAllSubscription(array $filters = [], $query = null)
    {
        $query = $this->getDefault('query', $query);
        $page = $this->getDefault('page');
        $itemsPerPage = $this->getDefault('itemsPerPage');
        $sortBy = $this->getDefault('sortBy', 'createdAt');
        $sortDir = $this->getDefault('sortDir', 1);

        $qb = $this->dm->createQueryBuilder($this->document);
        $qb = $this->addFilters($qb, $filters);
        $qb = $this->addQuery($qb, $this->queryFields, $query);

        $qb->field('event')->prime(true);
        $qb->field('user')->prime(true);
        $qb->field('organization')->prime(true);

        if ($sortBy) {
            $qb = $this->addSort($qb, $sortBy, $sortDir);
        }

        return $this->paginator->paginate($qb, $page, $itemsPerPage);
    }

    /**
     * Find all subscriptions of organizations from an event
     *
     * @param Event       $event         Event that organizations are susbriced
     * @param ArrayAccess $organizations Array of organizations
     *
     * @return ArrayCollection Collection of contacts
     */
    public function findEnabledByOrganizations(Event $event, $organizations)
    {
        $qb = $this->dm->createQueryBuilder($this->document);

        $ids = $this->getMongoIds($organizations);

        $query = $qb->field('organization.$id')->in($ids)
                    ->field('event.$id')->equals(new \MongoId($event->getId()))
                    ->field('enabled')->equals(true)
                    ->getQuery();

        return $query->execute();
    }

    /**
     * Find all subscriptions of user from an event
     *
     * @param Event       $event Event that organizations are susbriced
     * @param ArrayAccess $users Array of organizations
     *
     * @return ArrayCollection Collection of contacts
     */
    public function findEnabledByUsers(Event $event, $users)
    {
        $qb = $this->dm->createQueryBuilder($this->document);

        $ids = $this->getMongoIds($users);

        $query = $qb->field('user.$id')->in($ids)
                    ->field('event.$id')->equals(new \MongoId($event->getId()))
                    ->field('enabled')->equals(true)
                    ->getQuery();

        return $query->execute();
    }

    /**
     * Find all subscriptions of organizations from an event
     *
     * @param Event       $event         Event that organizations are susbriced
     * @param ArrayAccess $organizations Array of organizations
     *
     * @return ArrayCollection Collection of contacts
     */
    public function findSubscribedUsers(Event $event, $organizations)
    {
        $qb = $this->dm->createQueryBuilder($this->document);

        $ids = $this->getMongoIds($organizations);

        $query = $qb->field('organization.$id')->in($ids)
                    ->field('event.$id')->equals(new \MongoId($event->getId()))
                    ->getQuery();

        $subscriptions = $query->execute();

        $users = [];
        foreach ($subscriptions as $subscription) {
            $users[] = $subscription->getUser();
        }

        return $users;
    }

    /**
     * @param Event $event Event that contacts are susbriced
     *
     * @return ArrayCollection Collection of contacts
     */
    public function findByUser(Event $event, Array $users)
    {
        $qb = $this->dm->createQueryBuilder($this->document);

        $ids = $this->getMongoIds($users);

        $query = $qb->field('user.$id')->in($ids)
                    ->field('event.$id')->equals(new \MongoId($event->getId()))
                    ->getQuery();

        return $query->execute();
    }

    public function findVisibleByUser(User $user)
    {
        $qb = $this->dm->createQueryBuilder($this->document);

        $query = $qb->field('user.$id')->equals(new \MongoId($user->getId()))
                    ->field('organization.$id')->equals(new \MongoId($user->getActiveOrganization()->getId()))
                    ->field('visible')->equals(true)
                    ->getQuery();

        return $query->execute();
    }

    /**
     * Find matched Roles
     *
     * @param Array $roles Roles to test
     *
     * @return Array $matched All reachable roles
     */
    public function findMatchedRoles($userRoles, $eventRoles)
    {
        foreach ($userRoles as $roleName) {
            $rolesObj[] = new Role($roleName);
        }

        $reachableRolesObj = $this->roleHierarchy->getReachableRoles($rolesObj);

        foreach ($reachableRolesObj as $roleObj) {
            $reachableRoles[] = $roleObj->getRole();
        }

        return array_intersect($eventRoles, $reachableRoles);
    }

    /**
     *  Update subscriptions based on Roles, and add new existing subscriptions
     *
     * @param User $user
     * @param OrganizationInterface $organization
     */
    public function updateSubscriptions(
        User $user,
        OrganizationInterface $organization,
        $events = null
    ) {
        if (!$events) {
            $events = $this->dm->getRepository('WobbleCodeNotificationBundle:Event')->findAll();
        }

        $role = $this->dm->getRepository('WobbleCodeUserBundle:Role')->findOneBy(
            array(
                'organization.$id' => new \MongoId($organization->getId()),
                'user.$id'         => new \MongoId($user->getId())
            )
        );

        foreach ($events as $event) {
            $this->updateSubscription($user, $organization, $role, $event, true);
        }

        $this->dm->flush();
    }

    /**
     * Upsert Subscription based on Role permmissions
     *
     * @param User         $user
     * @param OrganizationInterface $organization
     * @param Role         $role
     * @param Event        $event
     * @param boolean      $flush
     */
    public function updateSubscription(
        User $user,
        OrganizationInterface $organization,
        $role,
        $event,
        $noFlush = null
    ) {

        $userRoles = array_merge($role->getRoles(), array_filter($user->getRoles(), function ($value) {
            if (strpos($value, 'ROLE_ORGANIZATION_') === false) {
                return $value;
            }
        }));

        $matchedRoles = $this->findMatchedRoles($userRoles, $event->getRoles());

        $subscription = $this->dm
            ->getRepository('WobbleCodeNotificationBundle:Subscription')
            ->findOneBy(
                array(
                    'event.$id'        => new \MongoId($event->getId()),
                    'user.$id'         => new \MongoId($user->getId()),
                    'organization.$id' => new \MongoId($organization->getId())
                )
            );

        if ($matchedRoles) {
            $defaults = $event->getDefaults();

            if (!$subscription) {
                $subscription = new Subscription;
                $subscription->setEnabled(in_array("enabled", $defaults));
            }

            $oldChannels = $subscription->getChannels();
            $subscription->setChannels([]);
            foreach ($event->getChannels() as $channel) {
                $channel = $channel->getName();

                $subscription->setChannel($channel, false);
                if (array_key_exists($channel, $oldChannels)) {
                    $subscription->setChannel($channel, $oldChannels[$channel]);
                } elseif (in_array($channel, $event->getDefaults())) {
                    $subscription->setChannel($channel, true);
                }

            }

            $subscription->setEvent($event);
            $subscription->setUser($user);
            $subscription->setOrganization($organization);
            $subscription->setVisible(in_array("visible", $defaults));

            $this->dm->persist($subscription);
        }

        if ($subscription && $matchedRoles == false) {
            $this->dm->remove($subscription);
        }

        if (!$noFlush) {
            $this->dm->flush();
        }
    }

    /**
     * Update all user/Organizaiton subscriptions for an Event. This is useful
     * when you create a new Event or the Roles of this event have changed
     *
     * This is a heavy task and should be used in a command only
     *
     * @param Array $events Array of events, to defined what events will be updated
     */
    public function updateAllSubscriptions($events = null)
    {
        $roles = $this->dm->getRepository('WobbleCodeUserBundle:Role')->findAll();

        foreach ($roles as $role) {
            foreach ($events as $event) {
                $this->updateSubscription(
                    $role->getUser(),
                    $role->getOrganization(),
                    $role,
                    $event,
                    false
                );
            }
        }
    }
}

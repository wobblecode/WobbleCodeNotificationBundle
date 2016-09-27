<?php

namespace WobbleCode\NotificationBundle\Document;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique;

/**
 * @MongoDB\Document(
 *     repositoryClass="WobbleCode\NotificationBundle\Document\SubscriptionRepository"
 * )
 * @Unique(
 *     repositoryMethod="findUniqueBy",
 *     fields={"event", "user", "organization"},
 *     errorPath="event",
 *     message="wc_notification.subscription.already_exists"
 * )
 * @Serializer\ExclusionPolicy("all")
 */
class Subscription
{
    /**
     * @MongoDB\Id(strategy="auto")
     * @Serializer\Groups({"ui", "api"})
     * @Serializer\Expose
     */
    protected $id;

    /**
     * @MongoDB\Field(type="boolean")
     * @Serializer\Expose
     */
    protected $enabled = true;

    /**
     * @MongoDB\Field(type="boolean")
     * @Serializer\Expose
     */
    protected $visible = true;

    /**
     * @MongoDB\Hash
     * @Serializer\Expose
     */
    protected $channels = [];

    /**
     * @MongoDB\Field(type="string")
     */
    protected $expressionRule;

    /**
     * @Gedmo\Timestampable(on="create")
     * @MongoDB\Field(type="date")
     * @Assert\DateTime()
     */
    protected $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @MongoDB\Field(type="date")
     * @Assert\DateTime()
     */
    protected $updatedAt;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Event")
     * @Assert\NotNull()
     * @Serializer\Expose
     * @Serializer\MaxDepth(1)
     */
    protected $event;

    /**
     * @MongoDB\ReferenceOne(
     *     targetDocument="WobbleCode\UserBundle\Document\User",
     *     inversedBy="notificationSubscriptions"
     * )
     * @Assert\NotNull()
     * @Serializer\Expose
     * @Serializer\MaxDepth(1)
     */
    protected $user;

    /**
     * @MongoDB\ReferenceOne(
     *     targetDocument="WobbleCode\UserBundle\Model\OrganizationInterface",
     *     inversedBy="notificationSubscriptions"
     * )
     * @Assert\NotNull()
     * @Serializer\Expose
     * @Serializer\MaxDepth(1)
     */
    protected $organization;

    /**
     * Get id
     *
     * @return id $id
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set enabled
     *
     * @param boolean $enabled
     * @return self
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * Get enabled
     *
     * @return boolean $enabled
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Set visible
     *
     * @param boolean $visible
     * @return self
     */
    public function setVisible($visible)
    {
        $this->visible = $visible;
        return $this;
    }

    /**
     * Get visible
     *
     * @return boolean $visible
     */
    public function getVisible()
    {
        return $this->visible;
    }

    /**
     * Set channels
     *
     * @param string[] $channels
     * @return self
     */
    public function setChannels($channels)
    {
        $this->channels = $channels;
        return $this;
    }

    /**
     * Set channels
     *
     * @param string[] $channel
     * @param boolean $value
     * @return self
     */
    public function setChannel($channel, $value)
    {
        $this->channels[$channel] = $value;
        return $this;
    }

    /**
     * Get channels
     *
     * @return boolean $channels
     */
    public function getChannels()
    {
        return $this->channels;
    }

    /**
     * Get channels
     *
     * @return boolean $channels
     */
    public function getChannel($channel)
    {
        return $this->channels[$channel];
    }
    
    /**
     * Set createdAt
     *
     * @param date $createdAt
     * @return self
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return date $createdAt
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param date $updatedAt
     * @return self
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return date $updatedAt
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set event
     *
     * @param WobbleCode\NotificationBundle\Document\Event $event
     * @return self
     */
    public function setEvent(\WobbleCode\NotificationBundle\Document\Event $event)
    {
        $this->event = $event;
        return $this;
    }

    /**
     * Get event
     *
     * @return WobbleCode\NotificationBundle\Document\Event $event
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set user
     *
     * @param WobbleCode\UserBundle\Document\User $user
     * @return self
     */
    public function setUser(\WobbleCode\UserBundle\Document\User $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get user
     *
     * @return WobbleCode\UserBundle\Document\User $user
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set organization
     *
     * @param WobbleCode\UserBundle\Model\OrganizationInterface $organization
     * @return self
     */
    public function setOrganization(\WobbleCode\UserBundle\Model\OrganizationInterface $organization)
    {
        $this->organization = $organization;
        return $this;
    }

    /**
     * Get organization
     *
     * @return WobbleCode\UserBundle\Model\OrganizationInterface $organization
     */
    public function getOrganization()
    {
        return $this->organization;
    }

    /**
     * Set expressionRule
     *
     * @param string $expressionRule
     * @return self
     */
    public function setExpressionRule($expressionRule)
    {
        $this->expressionRule = $expressionRule;
        return $this;
    }

    /**
     * Get expressionRule
     *
     * @return string $expressionRule
     */
    public function getExpressionRule()
    {
        return $this->expressionRule;
    }
}

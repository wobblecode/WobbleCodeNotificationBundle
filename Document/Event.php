<?php

namespace WobbleCode\NotificationBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique;
use Gedmo\Mapping\Annotation as Gedmo;
use WobbleCode\UserBundle\Model\OrganizationInterface;
use WobbleCode\ManagerBundle\Traits\Document\Taggeable;

/**
 * @MongoDB\Document
 * @MongoDB\UniqueIndex(keys={"key"="asc"})
 * @Unique(fields={"key"}, errorPath="key", message="This key already exists")
 * @Serializer\ExclusionPolicy("all")
 */
class Event
{
    /**
     * Trait to add tags support
     */
    use Taggeable;

    /**
     * @MongoDB\Id(strategy="auto")
     * @Serializer\Groups({"ui", "api"})
     * @Serializer\Expose
     */
    protected $id;

    /**
     * @MongoDB\Field(type="string")
     * @Assert\Length(max = "255")
     */
    protected $description;

    /**
     * @MongoDB\Field(type="boolean")
     * @Serializer\Expose
     */
    protected $manual = false;

    /**
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(max = "128")
     * @Serializer\Expose
     */
    protected $key;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $expressionRule;

    /**
     * @MongoDB\Hash
     */
    protected $defaults;

    /**
     * @MongoDB\Hash
     * @Assert\Type(type="array")
     * @Assert\NotNull()
     * @Serializer\Expose
     */
    protected $roles;

    /**
     * @MongoDB\ReferenceMany(targetDocument="WobbleCode\NotificationBundle\Document\Channel")
     */
    protected $channels;

    /**
     * @MongoDB\Hash
     * @Assert\Type(type="array")
     * @Assert\NotNull()
     * @Serializer\Expose
     */
    protected $channelTemplates = [];
    
    /**
     * levels from syslog protocol defined in RFC 5424
     *
     *     100 = DEBUG
     *     200 = INFO
     *     250 = NOTICE
     *     280 = SUCCESS (Not RFC standard)
     *     300 = WARNING
     *     400 = ERROR
     *     500 = CRITICAL
     *     550 = ALERT
     *     600 = EMERGENCY
     *
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(max="16")
     * @Serializer\Expose
     */
    protected $level = 100;

    /**
     * Mapping Level names
     *
     * @var array
     */
    protected $levelNames = [
        100 => 'DEBUG',
        200 => 'INFO',
        250 => 'NOTICE',
        280 => 'SUCCESS',
        300 => 'WARNING',
        400 => 'ERROR',
        500 => 'CRITICAL',
        550 => 'ALERT',
        600 => 'EMERGENCY',
    ];

    /**
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     */
    protected $uiTemplate = 'WobbleCodeNotificationBundle::Notifications/default.ui.html.twig';

    /**
     * @MongoDB\Field(type="string")
     */
    protected $fromEmail;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $fromName;
    
    /**
     * @MongoDB\Collection
     */
    protected $bcc = [];

    /**
     * @MongoDB\ReferenceMany(targetDocument="Notification", mappedBy="event")
     */
    private $notifications;

    public function __construct()
    {
        $this->notifications = new \Doctrine\Common\Collections\ArrayCollection();
        $this->channels = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function __toString()
    {
        return $this->key;
    }

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
     * Set manual
     *
     * @param boolean $manual
     * @return self
     */
    public function setManual($manual)
    {
        $this->manual = $manual;
        return $this;
    }

    /**
     * Get manual
     *
     * @return boolean $manual
     */
    public function getManual()
    {
        return $this->manual;
    }

    /**
     * Set key
     *
     * @param string $key
     * @return self
     */
    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    /**
     * Get key
     *
     * @return string $key
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * Set defaults
     *
     * @param hash $defaults
     * @return self
     */
    public function setDefaults($defaults)
    {
        $this->defaults = $defaults;
        return $this;
    }

    /**
     * Get defaults
     *
     * @return hash $defaults
     */
    public function getDefaults()
    {
        return $this->defaults;
    }

    /**
     * Set description
     *
     * @param string $description
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set ui template
     *
     * @param string $uiTemplate
     * @return self
     */
    public function setUiTemplate($uiTemplate)
    {
        $this->uiTemplate = $uiTemplate;
        return $this;
    }

    /**
     * Get ui template
     *
     * @return string $uiTemplate
     */
    public function getUiTemplate()
    {
        return $this->uiTemplate;
    }

    /**
     * Set roles
     *
     * @param hash $roles
     * @return self
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
        return $this;
    }

    /**
     * Get roles
     *
     * @return hash $roles
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Add channel
     *
     * @param Channel $channel
     */
    public function addChannel(Channel $channel)
    {
        $this->channels[] = $channel;
    }

    /**
     * Remove channel
     *
     * @param Channel $channel
     */
    public function removeChannel(Channel $channel)
    {
        $this->channels->removeElement($channel);
    }

    /**
     * Get channels
     *
     * @return \Doctrine\Common\Collections\Collection $channels
     */
    public function getChannels()
    {
        return $this->channels;
    }

    /**
     * Set channelTemplates
     *
     * @param hash $channelTemplates
     * @return self
     */
    public function setChannelTemplates($channelTemplates)
    {
        $this->channelTemplates = $channelTemplates;
        return $this;
    }

    /**
     * Get channelTemplates
     *
     * @return hash $channelTemplates
     */
    public function getChannelTemplates()
    {
        return $this->channelTemplates;
    }

    /**
     * Get channelTemplates
     *
     * @return string
     */
    public function getChannelTemplate($channel)
    {
        return $this->channelTemplates[$channel];
    }
    
    /**
     * Set level
     *
     * @param string $level
     * @return self
     */
    public function setLevel($level)
    {
        $this->level = $level;
        return $this;
    }

    /**
     * Get level
     *
     * @return string $level
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Get level name
     *
     * @Serializer\VirtualProperty
     * @Serializer\Type("string")
     * @Serializer\SerializedName("level_name")
     * @Serializer\Groups({"ui", "api"})
     *
     * @return string
     */
    public function getLevelName()
    {
        return $this->levelNames[$this->level];
    }

    /**
     * Add notification
     *
     * @param WobbleCode\NotificationBundle\Document\Notification $notification
     */
    public function addNotification(\WobbleCode\NotificationBundle\Document\Notification $notification)
    {
        $this->notifications[] = $notification;
    }

    /**
     * Remove notification
     *
     * @param WobbleCode\NotificationBundle\Document\Notification $notification
     */
    public function removeNotification(\WobbleCode\NotificationBundle\Document\Notification $notification)
    {
        $this->notifications->removeElement($notification);
    }

    /**
     * Get notifications
     *
     * @return \Doctrine\Common\Collections\Collection $notifications
     */
    public function getNotifications()
    {
        return $this->notifications;
    }

    /**
     * Set bcc
     *
     * @param collection $bcc
     * @return self
     */
    public function setBcc($bcc)
    {
        $this->bcc = $bcc;
        return $this;
    }

    /**
     * Get bcc
     *
     * @return collection $bcc
     */
    public function getBcc()
    {
        return $this->bcc;
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

    /**
     * Set fromEmail
     *
     * @param string $fromEmail
     * @return self
     */
    public function setFromEmail($fromEmail)
    {
        $this->fromEmail = $fromEmail;
        return $this;
    }

    /**
     * Get fromEmail
     *
     * @return string $fromEmail
     */
    public function getFromEmail()
    {
        return $this->fromEmail;
    }

    /**
     * Set fromName
     *
     * @param string $fromName
     * @return self
     */
    public function setFromName($fromName)
    {
        $this->fromName = $fromName;
        return $this;
    }

    /**
     * Get fromName
     *
     * @return string $fromName
     */
    public function getFromName()
    {
        return $this->fromName;
    }
}

<?php

namespace WobbleCode\NotificationBundle\Document;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation as Serializer;

/**
 * @MongoDB\Document()
 * @Serializer\ExclusionPolicy("all")
 */
class Notification
{
    /**
     * @MongoDB\Id(strategy="auto")
     * @Serializer\Groups({"ui", "api"})
     * @Serializer\Expose
     */
    protected $id;

    /**
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(max="255")
     * @Serializer\Expose
     */
    protected $title;

    /**
     * @MongoDB\Field(type="string")
     */
    protected $description;

    /**
     * read, unread
     *
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(max="16")
     * @Serializer\Expose
     */
    protected $status = 'unread';

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
     * @Assert\Length(max="40")
     */
    protected $interfacePlacement;

    /**
     * @MongoDB\Field(type="boolean")
     * @Assert\Type(type="bool")
     * @Serializer\Expose
     */
    protected $actionRequired;

    /**
     * pending, completed
     *
     * @MongoDB\Field(type="string")
     * @Assert\NotBlank()
     * @Assert\Length(max="16")
     * @Serializer\Expose
     */
    protected $actionStatus;

    /**
     * @MongoDB\Field(type="date")
     * @Assert\DateTime()
     * @Serializer\Expose
     */
    protected $actionAt;

    /**
     * @MongoDB\Field(type="date")
     * @Assert\DateTime()
     * @Serializer\Expose
     */
    protected $readAt;

    /**
     * @Gedmo\Timestampable(on="create")
     * @MongoDB\Field(type="date")
     * @Assert\DateTime()
     * @Serializer\Expose
     */
    protected $createdAt;

    /**
     * @Gedmo\Timestampable(on="update")
     * @MongoDB\Field(type="date")
     * @Assert\DateTime()
     */
    protected $updatedAt;

    /**
     * @MongoDB\Field(type="date")
     */
    protected $deletedAt;

    /**
     * @MongoDB\ReferenceOne(targetDocument="Event", inversedBy="notifications")
     * @Assert\NotNull()
     */
    private $event;

    /**
     * @MongoDB\ReferenceOne(targetDocument="WobbleCode\UserBundle\Document\User")
     * @Assert\NotNull()
     * @Serializer\Expose
     * @Serializer\MaxDepth(1)
     */
    private $user;

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
     * Set title
     *
     * @param string $title
     * @return self
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Get title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
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
     * Set status
     *
     * @param string $status
     * @return self
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return string $status
     */
    public function getStatus()
    {
        return $this->status;
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
     * Set interfacePlacement
     *
     * @param string $interfacePlacement
     * @return self
     */
    public function setInterfacePlacement($interfacePlacement)
    {
        $this->interfacePlacement = $interfacePlacement;
        return $this;
    }

    /**
     * Get interfacePlacement
     *
     * @return string $interfacePlacement
     */
    public function getInterfacePlacement()
    {
        return $this->interfacePlacement;
    }

    /**
     * Set actionRequired
     *
     * @param boolean $actionRequired
     * @return self
     */
    public function setActionRequired($actionRequired)
    {
        $this->actionRequired = $actionRequired;
        return $this;
    }

    /**
     * Get actionRequired
     *
     * @return boolean $actionRequired
     */
    public function getActionRequired()
    {
        return $this->actionRequired;
    }

    /**
     * Set actionStatus
     *
     * @param string $actionStatus
     * @return self
     */
    public function setActionStatus($actionStatus)
    {
        $this->actionStatus = $actionStatus;
        return $this;
    }

    /**
     * Get actionStatus
     *
     * @return string $actionStatus
     */
    public function getActionStatus()
    {
        return $this->actionStatus;
    }

    /**
     * Set actionAt
     *
     * @param date $actionAt
     * @return self
     */
    public function setActionAt($actionAt)
    {
        $this->actionAt = $actionAt;
        return $this;
    }

    /**
     * Get actionAt
     *
     * @return date $actionAt
     */
    public function getActionAt()
    {
        return $this->actionAt;
    }

    /**
     * Set readAt
     *
     * @param date $readAt
     * @return self
     */
    public function setReadAt($readAt)
    {
        $this->readAt = $readAt;
        return $this;
    }

    /**
     * Get readAt
     *
     * @return date $readAt
     */
    public function getReadAt()
    {
        return $this->readAt;
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
     * Set deletedAt
     *
     * @param date $deletedAt
     * @return self
     */
    public function setDeletedAt($deletedAt)
    {
        $this->deletedAt = $deletedAt;
        return $this;
    }

    /**
     * Get deletedAt
     *
     * @return date $deletedAt
     */
    public function getDeletedAt()
    {
        return $this->deletedAt;
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
}

<?php

namespace WobbleCode\NotificationBundle\Model;

use Symfony\Component\Validator\Constraints as Assert;

class Notify
{
    /**
     * @Assert\NotNull()
     */
    protected $event;

    /**
     * @Assert\NotNull()
     */
    protected $users;

    /**
     * @Assert\NotNull()
     */
    protected $organizations;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max="16")
     */
    protected $level = 200;

    /**
     * @Assert\Length(max = "2")
     */
    protected $language = false;

    /**
     * @Assert\Length(max = "40")
     */
    protected $interfacePlacement = false;

    /**
     * @Assert\Type(type="bool")
     */
    protected $actionRequired;

    protected $forceChannels;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(max="255")
     */
    protected $subject;

    /**
     * @Assert\NotBlank()
     */
    protected $message;

    /**
     * Set event
     *
     * @param string $event
     * @return NotifyOrganization
     */
    public function setEvent($event)
    {
        $this->event = $event;

        return $this;
    }

    /**
     * Get event
     *
     * @return string
     */
    public function getEvent()
    {
        return $this->event;
    }

    /**
     * Set organizations
     *
     * @param string $organizations
     * @return NotifyOrganization
     */
    public function setOrganizations($organizations)
    {
        $this->organizations = $organizations;

        return $this;
    }

    /**
     * Get organizations
     *
     * @return string
     */
    public function getOrganizations()
    {
        return $this->organizations;
    }

    /**
     * Set users
     *
     * @param string $users
     * @return NotifyUser
     */
    public function setUsers($users)
    {
        $this->users = $users;

        return $this;
    }

    /**
     * Get users
     *
     * @return string
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * Set level
     *
     * @param string $level
     * @return NotifyOrganization
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return string
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set language
     *
     * @param string $language
     * @return NotifyOrganization
     */
    public function setLanguage($language)
    {
        $this->language = $language;

        return $this;
    }

    /**
     * Get language
     *
     * @return string
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set interfacePlacement
     *
     * @param string $interfacePlacement
     * @return NotifyOrganization
     */
    public function setInterfacePlacement($interfacePlacement)
    {
        $this->interfacePlacement = $interfacePlacement;

        return $this;
    }

    /**
     * Get interfacePlacement
     *
     * @return string
     */
    public function getInterfacePlacement()
    {
        return $this->interfacePlacement;
    }

    /**
     * Set actionRequired
     *
     * @param string $actionRequired
     * @return NotifyOrganization
     */
    public function setActionRequired($actionRequired)
    {
        $this->actionRequired = $actionRequired;

        return $this;
    }

    /**
     * Get actionRequired
     *
     * @return string
     */
    public function getActionRequired()
    {
        return $this->actionRequired;
    }

    /**
     * Set forceChannels
     *
     * @param string $forceChannels
     * @return NotifyOrganization
     */
    public function setForceChannels($forceChannels)
    {
        $this->forceChannels = $forceChannels;

        return $this;
    }

    /**
     * Get forceChannels
     *
     * @return string
     */
    public function getForceChannels()
    {
        return $this->forceChannels;
    }

    /**
     * Set subject
     *
     * @param string $subject
     * @return NotifyOrganization
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return NotifyOrganization
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }
}

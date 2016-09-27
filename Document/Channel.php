<?php

namespace WobbleCode\NotificationBundle\Document;

use Gedmo\Mapping\Annotation as Gedmo;
use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use Doctrine\Bundle\MongoDBBundle\Validator\Constraints\Unique;
use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @MongoDB\Document
 * @Serializer\ExclusionPolicy("all")
 * @Unique(
 *     fields={"name"},
 *     errorPath="name",
 *     message="Duplicated Name"
 * )
 */
class Channel
{
    /**
     * @MongoDB\Id
     * @Serializer\Expose
     * @Serializer\Groups({"ui", "api"})
     */
    protected $id;

    /**
     * Name of the route
     *
     * @MongoDB\Field(type="string")
     * @Serializer\Expose
     * @Serializer\Groups({"ui", "api"})
     */
    protected $name;

    /**
     * The DI service name to load
     *
     * @MongoDB\Field(type="string")
     * @Serializer\Expose
     * @Serializer\Groups({"ui", "api"})
     */
    protected $service;

    /**
     * Extra setting params
     *
     * @MongoDB\Hash
     * @Serializer\Expose
     * @Serializer\Groups({"ui", "api"})
     */
    protected $params = [];


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
     * Set service
     *
     * @param string $service
     *
     * @return self
     */
    public function setService($service)
    {
        $this->service = $service;

        return $this;
    }

    /**
     * Get service
     *
     * @return string $service
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * Set params
     *
     * @param hash $params
     *
     * @return self
     */
    public function setParams($params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * Get params
     *
     * @return hash $params
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string $name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     *
     * @return string Full name
     */
    public function __toString()
    {
        return (string) $this->getName();
    }
}

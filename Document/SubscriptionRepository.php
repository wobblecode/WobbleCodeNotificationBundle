<?php

namespace WobbleCode\NotificationBundle\Document;

use Doctrine\ODM\MongoDB\DocumentRepository;

class SubscriptionRepository extends DocumentRepository
{
    public function findUniqueBy($criteria)
    {
        return $this->findBy([
            'organization.$id' => new \MongoId($criteria['organization']->getId()),
            'user.$id' => new \MongoId($criteria['user']->getId()),
            'event.$id' => new \MongoId($criteria['event']->getId())
        ]);
    }
}

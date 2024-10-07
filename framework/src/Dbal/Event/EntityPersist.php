<?php

namespace danyk\Framework\Dbal\Event;

use danyk\Framework\Dbal\Entity;
use danyk\Framework\Event\Event;

class EntityPersist extends Event
{
    public function __construct(
        private Entity $entity
    ) {
    }

    public function getEntity(): Entity
    {
        return $this->entity;
    }
}

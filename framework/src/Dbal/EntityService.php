<?php

namespace danyk\Framework\Dbal;

use danyk\Framework\Dbal\Event\EntityPersist;
use danyk\Framework\Event\EventDispatcher;
use Doctrine\DBAL\Connection;

class EntityService
{
    public function __construct(
        private Connection $connection,
        private EventDispatcher $eventDispatcher
    ) {
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }

    public function save(Entity $entity): int
    {
        $entityId = $this->connection->lastInsertId();
        $entity->setId($entityId);

        $this->eventDispatcher->dispatch(new EntityPersist($entity));

        return $entityId;
    }
}
